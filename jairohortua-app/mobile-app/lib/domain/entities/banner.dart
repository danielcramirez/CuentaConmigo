class Banner {
  final int id;
  final String imageUrl;
  final String? targetUrl;
  final int order;
  final bool isActive;
  final DateTime updatedAt;

  Banner({
    required this.id,
    required this.imageUrl,
    this.targetUrl,
    required this.order,
    required this.isActive,
    required this.updatedAt,
  });

  factory Banner.fromJson(Map<String, dynamic> json) {
    return Banner(
      id: json['id'],
      imageUrl: json['image_url'],
      targetUrl: json['target_url'],
      order: json['order'],
      isActive: json['is_active'],
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'image_url': imageUrl,
      'target_url': targetUrl,
      'order': order,
      'is_active': isActive,
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}
