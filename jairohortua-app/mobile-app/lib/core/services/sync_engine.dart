import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:dio/dio.dart';
import 'package:jairohortua_app/core/database/database_helper.dart';
import 'package:jairohortua_app/core/network/http_client.dart';

class SyncEngine {
  final HttpClient _httpClient = HttpClient();
  final DatabaseHelper _db = DatabaseHelper();
  final Connectivity _connectivity = Connectivity();

  Future<void> syncIfConnected() async {
    final status = await _connectivity.checkConnectivity();
    if (status == ConnectivityResult.none) return;

    try {
      await _pullUpdates();
      await _pushPendingOperations();
    } catch (_) {
      // Ignore errors, retry later
    }
  }

  Future<void> _pullUpdates() async {
    final lastSync = await _db.getLastSyncAt();
    final Response response = await _httpClient.dio.get('/sync/pull', queryParameters: {
      if (lastSync != null) 'since': lastSync,
    });

    final profileResponse = await _httpClient.dio.get('/users/me');
    final profileData = Map<String, dynamic>.from(profileResponse.data as Map<String, dynamic>);
    await _db.upsertProfile({
      'user_id': profileData['id'],
      'username': profileData['username'],
      'email': profileData['email'],
      'referral_code': profileData['referral_code'],
      'role': (profileData['roles'] as List<dynamic>).isNotEmpty
          ? (profileData['roles'] as List<dynamic>).first.toString()
          : '',
      'updated_at': DateTime.now().toIso8601String(),
    });

    final data = response.data as Map<String, dynamic>;
    final changes = data['changes'] as Map<String, dynamic>;

    final events = (changes['events'] as List<dynamic>).cast<Map<String, dynamic>>();
    final banners = (changes['banners'] as List<dynamic>).cast<Map<String, dynamic>>();
    final notifications = (changes['notifications'] as List<dynamic>).cast<Map<String, dynamic>>();
    final referrals = (changes['referrals'] as List<dynamic>).cast<Map<String, dynamic>>();

    if (events.isNotEmpty) {
      await _db.replaceEvents(events.map((e) {
        return {
          'server_id': e['id'],
          'title': e['title'],
          'description': e['description'],
          'image_url': e['image_url'],
          'latitude': e['latitude'],
          'longitude': e['longitude'],
          'starts_at': e['starts_at'],
          'updated_at': e['updated_at'].toString(),
        };
      }).toList());
    }

    if (banners.isNotEmpty) {
      final banner = banners.first;
      await _db.replaceBanner({
        'server_id': banner['id'],
        'image_url': banner['image_url'],
        'target_url': banner['target_url'],
        'is_active': banner['is_active'] ? 1 : 0,
        'updated_at': banner['updated_at'].toString(),
      });
    }

    if (notifications.isNotEmpty) {
      await _db.replaceNotifications(notifications.map((n) {
        return {
          'server_id': n['id'],
          'title': n['title'],
          'message': n['message'],
          'type': n['type'],
          'read_at': n['read_at']?.toString(),
          'updated_at': n['updated_at'].toString(),
        };
      }).toList());
    }

    if (referrals.isNotEmpty) {
      await _db.replaceReferrals(referrals.map((r) {
        return {
          'server_id': r['id'],
          'referrer_id': r['referrer_id'],
          'referred_id': r['referred_id'],
          'status': r['status'],
          'updated_at': r['updated_at'].toString(),
        };
      }).toList());
    }

    if (data['server_time'] != null) {
      await _db.updateLastSyncAt(data['server_time'].toString());
    }
  }

  Future<void> _pushPendingOperations() async {
    final pending = await _db.getPendingOperations();
    if (pending.isEmpty) return;

    final operations = pending.map((op) {
      return {
        'client_uuid': op['client_uuid'],
        'op_type': op['operation_type'],
        'payload': _decodePayload(op['payload']),
      };
    }).toList();

    await _httpClient.dio.post('/sync/push', data: {
      'operations': operations,
    });

    final ids = pending.map((op) => op['id'] as String).toList();
    await _db.markOperationsAsSynced(ids);
  }

  Map<String, dynamic> _decodePayload(Object? payload) {
    if (payload is String) {
      return Map<String, dynamic>.from(Uri.splitQueryString(payload));
    }
    if (payload is Map<String, dynamic>) {
      return payload;
    }
    return {};
  }
}
