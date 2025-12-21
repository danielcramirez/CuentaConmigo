import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:share_plus/share_plus.dart';
import 'package:jairohortua_app/core/database/database_helper.dart';
import 'package:jairohortua_app/core/services/sync_engine.dart';
import 'package:jairohortua_app/core/network/http_client.dart';
import 'package:jairohortua_app/presentation/providers/auth_provider.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const DashboardTab(),
    const EventsTab(),
    const NotificationsTab(),
    const ProfileTab(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _screens[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Inicio',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.event),
            label: 'Eventos',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.notifications),
            label: 'Notificaciones',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Perfil',
          ),
        ],
      ),
    );
  }
}

class DashboardTab extends StatefulWidget {
  const DashboardTab({Key? key}) : super(key: key);

  @override
  State<DashboardTab> createState() => _DashboardTabState();
}

class _DashboardTabState extends State<DashboardTab> {
  final DatabaseHelper _db = DatabaseHelper();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Dashboard')),
      body: FutureBuilder<Map<String, dynamic>?>(
        future: _db.getProfile(),
        builder: (context, snapshot) {
          final profile = snapshot.data;
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Card(
                child: ListTile(
                  title: Text(profile?['username']?.toString() ?? 'Sin perfil'),
                  subtitle: Text(profile?['email']?.toString() ?? 'Sin email'),
                  trailing: Text(profile?['role']?.toString() ?? ''),
                ),
              ),
              const SizedBox(height: 16),
              FutureBuilder<Map<String, dynamic>?>(
                future: _db.getActiveBanner(),
                builder: (context, bannerSnap) {
                  final banner = bannerSnap.data;
                  if (banner == null) {
                    return const SizedBox.shrink();
                  }
                  return Card(
                    child: ListTile(
                      title: Text(banner['image_url']?.toString() ?? ''),
                      subtitle: Text(banner['target_url']?.toString() ?? ''),
                    ),
                  );
                },
              ),
            ],
          );
        },
      ),
    );
  }
}

class EventsTab extends StatefulWidget {
  const EventsTab({Key? key}) : super(key: key);

  @override
  State<EventsTab> createState() => _EventsTabState();
}

class _EventsTabState extends State<EventsTab> {
  final DatabaseHelper _db = DatabaseHelper();
  final RefreshController _refreshController = RefreshController(initialRefresh: false);
  List<Map<String, dynamic>> _events = [];

  @override
  void initState() {
    super.initState();
    _loadEvents();
  }

  Future<void> _loadEvents() async {
    final events = await _db.getEvents();
    setState(() {
      _events = events;
    });
  }

  Future<void> _onRefresh() async {
    await SyncEngine().syncIfConnected();
    await _loadEvents();
    _refreshController.refreshCompleted();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Eventos')),
      body: SmartRefresher(
        controller: _refreshController,
        onRefresh: _onRefresh,
        child: ListView.builder(
          itemCount: _events.length,
          itemBuilder: (context, index) {
            final event = _events[index];
            return Card(
              child: ListTile(
                title: Text(event['title']?.toString() ?? ''),
                subtitle: Text(event['description']?.toString() ?? ''),
                trailing: IconButton(
                  icon: const Icon(Icons.share),
                  onPressed: () {
                    final text = 'Mira este evento en Jairohortua App! ${event['title'] ?? ''}';
                    Share.share(text);
                  },
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}

class NotificationsTab extends StatefulWidget {
  const NotificationsTab({Key? key}) : super(key: key);

  @override
  State<NotificationsTab> createState() => _NotificationsTabState();
}

class _NotificationsTabState extends State<NotificationsTab> {
  final DatabaseHelper _db = DatabaseHelper();
  List<Map<String, dynamic>> _notifications = [];

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    final notifications = await _db.getNotifications();
    setState(() {
      _notifications = notifications;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notificaciones')),
      body: ListView.builder(
        itemCount: _notifications.length,
        itemBuilder: (context, index) {
          final notif = _notifications[index];
          return Card(
            child: ListTile(
              title: Text(notif['title']?.toString() ?? ''),
              subtitle: Text(notif['message']?.toString() ?? ''),
            ),
          );
        },
      ),
    );
  }
}

class ProfileTab extends StatefulWidget {
  const ProfileTab({Key? key}) : super(key: key);

  @override
  State<ProfileTab> createState() => _ProfileTabState();
}

class _ProfileTabState extends State<ProfileTab> {
  final DatabaseHelper _db = DatabaseHelper();
  final HttpClient _client = HttpClient();
  Map<String, dynamic> _settings = {};

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    try {
      final response = await _client.dio.get('/settings/app');
      setState(() {
        _settings = Map<String, dynamic>.from(response.data as Map<String, dynamic>);
      });
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Perfil')),
      body: FutureBuilder<Map<String, dynamic>?>(
        future: _db.getProfile(),
        builder: (context, snapshot) {
          final profile = snapshot.data;
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Card(
                child: ListTile(
                  title: Text(profile?['username']?.toString() ?? 'Usuario'),
                  subtitle: Text(profile?['email']?.toString() ?? ''),
                ),
              ),
              const SizedBox(height: 16),
              ListTile(
                leading: const Icon(Icons.facebook),
                title: const Text('Facebook'),
                onTap: () {
                  final url = _settings['social_facebook_url']?.toString() ?? '';
                  if (url.isNotEmpty) {
                    Navigator.of(context).pushNamed('/social', arguments: {
                      'url': url,
                      'title': 'Facebook',
                    });
                  }
                },
              ),
              ListTile(
                leading: const Icon(Icons.camera_alt),
                title: const Text('Instagram'),
                onTap: () {
                  final url = _settings['social_instagram_url']?.toString() ?? '';
                  if (url.isNotEmpty) {
                    Navigator.of(context).pushNamed('/social', arguments: {
                      'url': url,
                      'title': 'Instagram',
                    });
                  }
                },
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () async {
                  await Provider.of<AuthProvider>(context, listen: false).logout();
                  if (!mounted) return;
                  Navigator.of(context).pushReplacementNamed('/login');
                },
                child: const Text('Cerrar sesion'),
              ),
            ],
          );
        },
      ),
    );
  }
}
