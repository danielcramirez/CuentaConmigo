import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';

class DatabaseHelper {
  static final DatabaseHelper _instance = DatabaseHelper._internal();
  factory DatabaseHelper() => _instance;
  DatabaseHelper._internal();

  static Database? _database;

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'jairohortua.db');
    return openDatabase(path, version: 1, onCreate: _onCreate);
  }

  Future<void> _onCreate(Database db, int version) async {
    await db.execute('''
      CREATE TABLE profile_cache(
        id INTEGER PRIMARY KEY,
        user_id INTEGER,
        username TEXT,
        email TEXT,
        referral_code TEXT,
        role TEXT,
        updated_at TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE events_cache(
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        title TEXT,
        description TEXT,
        image_url TEXT,
        latitude REAL,
        longitude REAL,
        starts_at TEXT,
        updated_at TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE banner_cache(
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        image_url TEXT,
        target_url TEXT,
        is_active INTEGER,
        updated_at TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE referrals_cache(
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        referrer_id INTEGER,
        referred_id INTEGER,
        status TEXT,
        updated_at TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE notifications_cache(
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        title TEXT,
        message TEXT,
        type TEXT,
        read_at TEXT,
        updated_at TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE pending_operations(
        id TEXT PRIMARY KEY,
        user_id INTEGER,
        operation_type TEXT,
        payload TEXT,
        status TEXT,
        client_uuid TEXT,
        created_at TEXT,
        attempts INTEGER DEFAULT 0
      )
    ''');

    await db.execute('''
      CREATE TABLE sync_metadata(
        id INTEGER PRIMARY KEY,
        last_sync_at TEXT
      )
    ''');

    await db.insert('sync_metadata', {'id': 1, 'last_sync_at': null});
  }

  Future<Map<String, dynamic>?> getProfile() async {
    final db = await database;
    final rows = await db.query('profile_cache', limit: 1);
    if (rows.isEmpty) return null;
    return rows.first;
  }

  Future<void> upsertProfile(Map<String, dynamic> profile) async {
    final db = await database;
    await db.delete('profile_cache');
    await db.insert('profile_cache', profile);
  }

  Future<List<Map<String, dynamic>>> getEvents({int limit = 50}) async {
    final db = await database;
    return db.query('events_cache', orderBy: 'starts_at DESC', limit: limit);
  }

  Future<void> replaceEvents(List<Map<String, dynamic>> events) async {
    final db = await database;
    await db.delete('events_cache');
    for (final event in events) {
      await db.insert('events_cache', event);
    }
  }

  Future<Map<String, dynamic>?> getActiveBanner() async {
    final db = await database;
    final rows = await db.query('banner_cache', orderBy: 'updated_at DESC', limit: 1);
    if (rows.isEmpty) return null;
    return rows.first;
  }

  Future<void> replaceBanner(Map<String, dynamic> banner) async {
    final db = await database;
    await db.delete('banner_cache');
    await db.insert('banner_cache', banner);
  }

  Future<List<Map<String, dynamic>>> getNotifications({int limit = 50}) async {
    final db = await database;
    return db.query('notifications_cache', orderBy: 'updated_at DESC', limit: limit);
  }

  Future<void> replaceNotifications(List<Map<String, dynamic>> notifications) async {
    final db = await database;
    await db.delete('notifications_cache');
    for (final notif in notifications) {
      await db.insert('notifications_cache', notif);
    }
  }

  Future<List<Map<String, dynamic>>> getReferrals() async {
    final db = await database;
    return db.query('referrals_cache', orderBy: 'updated_at DESC');
  }

  Future<void> replaceReferrals(List<Map<String, dynamic>> referrals) async {
    final db = await database;
    await db.delete('referrals_cache');
    for (final ref in referrals) {
      await db.insert('referrals_cache', ref);
    }
  }

  Future<List<Map<String, dynamic>>> getPendingOperations() async {
    final db = await database;
    return db.query('pending_operations', where: "status = 'pending'");
  }

  Future<void> addPendingOperation(Map<String, dynamic> operation) async {
    final db = await database;
    await db.insert('pending_operations', operation);
  }

  Future<void> markOperationsAsSynced(List<String> ids) async {
    if (ids.isEmpty) return;
    final db = await database;
    final idList = ids.map((id) => "'$id'").join(',');
    await db.rawUpdate("UPDATE pending_operations SET status = 'completed' WHERE id IN ($idList)");
  }

  Future<String?> getLastSyncAt() async {
    final db = await database;
    final rows = await db.query('sync_metadata', where: 'id = 1', limit: 1);
    if (rows.isEmpty) return null;
    return rows.first['last_sync_at'] as String?;
  }

  Future<void> updateLastSyncAt(String timestamp) async {
    final db = await database;
    await db.update('sync_metadata', {'last_sync_at': timestamp}, where: 'id = 1');
  }
}
