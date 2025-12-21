class AppConstants {
  // API
  static const String baseUrl = 'http://localhost:8000/api';
  static const String baseUrlProduction = 'https://app.jairohortua.com/api';
  
  // Storage keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String lastSyncKey = 'last_sync_at';
  static const String rolesKey = 'user_roles';
  static const String modulesKey = 'user_modules';
  
  // Timeouts
  static const Duration apiTimeout = Duration(seconds: 30);
  static const Duration tokenRefreshThreshold = Duration(minutes: 10);
  
  // Database
  static const String dbName = 'jairohortua.db';
  
  // Firebase
  static const String fcmTopic = 'jairohortua-app';
  
  // Pagination
  static const int pageSize = 50;
  
  // Offline sync
  static const Duration syncInterval = Duration(minutes: 5);
}
