import 'package:flutter/material.dart';
import 'package:jairohortua_app/presentation/screens/auth/login_screen.dart';
import 'package:jairohortua_app/presentation/screens/home/home_screen.dart';
import 'package:jairohortua_app/presentation/screens/social/social_webview_screen.dart';

class AppRouter {
  static Route<dynamic> generateRoute(RouteSettings settings) {
    switch (settings.name) {
      case '/login':
        return MaterialPageRoute(builder: (_) => const LoginScreen());
      case '/home':
        return MaterialPageRoute(builder: (_) => const HomeScreen());
      case '/social':
        final args = settings.arguments as Map<String, String>;
        return MaterialPageRoute(
          builder: (_) => SocialWebViewScreen(
            url: args['url'] ?? '',
            title: args['title'] ?? 'Social',
          ),
        );
      default:
        return MaterialPageRoute(
          builder: (_) => Scaffold(
            body: Center(
              child: Text('No route defined for ${settings.name}'),
            ),
          ),
        );
    }
  }
}
