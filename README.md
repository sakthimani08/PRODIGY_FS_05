project appears to be a simple social media web application in PHP, where users can:

* Register and log in
* Follow each other
* Like and comment on posts
* View notifications
* Navigate via a navbar
* View user profiles

---

## 📝 Extended Description

This is a **PHP-based social media web application** that allows users to interact with one another through following, liking, commenting, and managing their profiles. The key functionalities are:

### 1. **User Authentication**

* **`register.php`**: Handles user registration.
* **`login.php`**: Verifies credentials and starts a session.
* **`logout.php`**: Destroys session and logs the user out.
* **`config.php`**: Centralized DB configuration using `mysqli`.

### 2. **Profile and Navigation**

* **`navbar.php`**: Displays navigation options like Home, Profile, Notifications.
* **`profile.php`**: Shows a user’s information and their posts.

### 3. **Social Features**

* **`follow.php`**: Allows users to follow/unfollow others.
* **`like_comment.php`**: Handles likes and comments on posts.
* **`notifications.php`**: Shows alerts when someone likes, comments, or follows.

### 4. **Post Interaction**

* **`index.php`**: Home page showing feed/posts from users.
* Users can like or comment on posts from here.

---

## 🗄️ SQL Command for Database Setup

Here is a possible schema based on your application files:

```sql
-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts Table
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments Table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes Table
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (post_id, user_id)
);

-- Follows Table
CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    followed_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (follower_id, followed_id)
);

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('like', 'comment', 'follow') NOT NULL,
    source_user_id INT NOT NULL,
    post_id INT,
    read_status BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (source_user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);
