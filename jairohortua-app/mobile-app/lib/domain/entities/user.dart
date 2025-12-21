class User {
  final int id;
  final String username;
  final String email;
  final String? referralCode;
  final List<String> roles;
  final List<String> modules;

  User({
    required this.id,
    required this.username,
    required this.email,
    this.referralCode,
    required this.roles,
    required this.modules,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      username: json['username'],
      email: json['email'],
      referralCode: json['referral_code'],
      roles: List<String>.from(json['roles'] ?? []),
      modules: List<String>.from(json['modules'] ?? []),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'email': email,
      'referral_code': referralCode,
      'roles': roles,
      'modules': modules,
    };
  }
}
