import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:jairohortua_app/core/services/connectivity_service.dart';
import 'package:jairohortua_app/core/services/token_manager.dart';
import 'package:jairohortua_app/core/network/http_client.dart';
import 'package:jairohortua_app/core/utils/router.dart';
import 'package:jairohortua_app/core/database/database_helper.dart';
import 'package:jairohortua_app/core/services/sync_engine.dart';
import 'package:jairohortua_app/core/services/location_service.dart';
import 'package:jairohortua_app/presentation/providers/auth_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Inicializar servicios
  ConnectivityService().initialize();
  HttpClient().initialize();
  await DatabaseHelper().database;

  runApp(const JairohortuaApp());
}

class JairohortuaApp extends StatelessWidget {
  const JairohortuaApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
      ],
      child: MaterialApp(
        title: 'Jairohortua App',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          primarySwatch: Colors.blue,
          useMaterial3: true,
        ),
        onGenerateRoute: AppRouter.generateRoute,
        home: const SplashScreen(),
      ),
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuthentication();
  }

  Future<void> _checkAuthentication() async {
    await Future.delayed(const Duration(seconds: 2));

    final hasToken = await TokenManager().hasToken();

    if (!mounted) return;

    if (hasToken) {
      await SyncEngine().syncIfConnected();
      await LocationService().captureLocationOnce();
      Navigator.of(context).pushReplacementNamed('/home');
    } else {
      Navigator.of(context).pushReplacementNamed('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.account_circle,
              size: 120,
              color: Theme.of(context).primaryColor,
            ),
            const SizedBox(height: 24),
            Text(
              'Jairohortua App',
              style: Theme.of(context).textTheme.headlineLarge,
            ),
            const SizedBox(height: 16),
            const CircularProgressIndicator(),
          ],
        ),
      ),
    );
  }
}
