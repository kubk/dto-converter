// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!

class Category {
  final String id;
  final String title;
  final int rating;
  final List<Recipe> recipes;

  Category({
    required this.id,
    required this.title,
    required this.rating,
    required this.recipes,
  });
}

enum ColorEnum {
  RED,
  GREEN,
  BLUE,
}

class Recipe {
  final String id;
  final String? imageUrl;
  final String? url;
  final bool isCooked;
  final num weight;

  Recipe({
    required this.id,
    this.imageUrl,
    this.url,
    required this.isCooked,
    required this.weight,
  });
}

class User {
  final String id;
  final User? bestFriend;
  final List<User> friends;
  final ColorEnum themeColor;
  final dynamic stringOrInteger;

  User({
    required this.id,
    this.bestFriend,
    required this.friends,
    required this.themeColor,
    required this.stringOrInteger,
  });
}
