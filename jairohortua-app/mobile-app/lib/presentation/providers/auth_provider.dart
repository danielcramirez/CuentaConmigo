import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:jairohortua_app/core/network/http_client.dart';
import 'package:jairohortua_app/core/services/token_manager.dart';
import 'package:jairohortua_app/core/constants/app_constants.dart';

class AuthProvider extends ChangeNotifier {
  final HttpClient _client = HttpClient();
  bool _isLoading = false;
  String? _error;

  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<bool> login(String username, String password) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _client.dio.post('/auth/login', data: {
        'username': username,
        'password': password,
      });

      final data = response.data as Map<String, dynamic>;
      final token = data['access_token'] as String;
      await TokenManager().saveToken(token);

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(AppConstants.userKey, jsonEncode(data['user']));
      await prefs.setStringList(AppConstants.rolesKey, (data['roles'] as List).cast<String>());
      await prefs.setStringList(AppConstants.modulesKey, (data['modules'] as List).cast<String>());

      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _client.dio.post('/auth/logout');
    } catch (_) {}
    await TokenManager().clear();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.userKey);
    await prefs.remove(AppConstants.rolesKey);
    await prefs.remove(AppConstants.modulesKey);
    notifyListeners();
  }
}
