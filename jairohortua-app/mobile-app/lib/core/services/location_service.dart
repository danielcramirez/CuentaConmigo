import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:jairohortua_app/core/network/http_client.dart';

class LocationService {
  final HttpClient _client = HttpClient();

  Future<void> captureLocationOnce() async {
    final prefs = await SharedPreferences.getInstance();
    final lastCapture = prefs.getString('last_location_capture');
    final today = DateFormat('yyyy-MM-dd').format(DateTime.now());

    if (lastCapture == today) return;

    final permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.deniedForever) return;

    if (permission == LocationPermission.denied) {
      final requested = await Geolocator.requestPermission();
      if (requested == LocationPermission.denied || requested == LocationPermission.deniedForever) {
        return;
      }
    }

    try {
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.best,
      );

      await _client.dio.post('/location', data: {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'accuracy': position.accuracy,
        'timestamp': DateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'").format(DateTime.now().toUtc()),
      });

      await prefs.setString('last_location_capture', today);
    } catch (_) {
      // Ignore errors
    }
  }
}
