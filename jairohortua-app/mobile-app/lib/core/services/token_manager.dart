import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenManager {
  static final TokenManager _instance = TokenManager._internal();

  factory TokenManager() {
    return _instance;
  }

  TokenManager._internal();

  final _secureStorage = const FlutterSecureStorage();
  
  static const String _tokenKey = 'auth_token';
  static const String _refreshTokenKey = 'refresh_token';

  String? _cachedToken;

  Future<void> saveToken(String token) async {
    _cachedToken = token;
    await _secureStorage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    _cachedToken ??= await _secureStorage.read(key: _tokenKey);
    return _cachedToken;
  }

  Future<void> deleteToken() async {
    _cachedToken = null;
    await _secureStorage.delete(key: _tokenKey);
  }

  Future<bool> hasToken() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  Future<void> clear() async {
    _cachedToken = null;
    await _secureStorage.deleteAll();
  }
}
