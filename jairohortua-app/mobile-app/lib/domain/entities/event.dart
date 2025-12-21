class Event {
  final int id;
  final String title;
  final String? description;
  final String? imageUrl;
  final double latitude;
  final double longitude;
  final DateTime startsAt;
  final String createdByUsername;
  final DateTime createdAt;
  final DateTime updatedAt;
  final double? distanceKm;

  Event({
    required this.id,
    required this.title,
    this.description,
    this.imageUrl,
    required this.latitude,
    required this.longitude,
    required this.startsAt,
    required this.createdByUsername,
    required this.createdAt,
    required this.updatedAt,
    this.distanceKm,
  });

  factory Event.fromJson(Map<String, dynamic> json) {
    return Event(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      imageUrl: json['image_url'],
      latitude: (json['latitude'] as num).toDouble(),
      longitude: (json['longitude'] as num).toDouble(),
      startsAt: DateTime.parse(json['starts_at']),
      createdByUsername: json['created_by']['username'] ?? '',
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      distanceKm: json['distance_km'] != null
          ? (json['distance_km'] as num).toDouble()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'image_url': imageUrl,
      'latitude': latitude,
      'longitude': longitude,
      'starts_at': startsAt.toIso8601String(),
      'created_by_username': createdByUsername,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'distance_km': distanceKm,
    };
  }
}
