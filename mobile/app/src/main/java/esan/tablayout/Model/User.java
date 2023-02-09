package esan.tablayout.Model;

public class User {

    private int id;
    private String name, username, email, avatar;

    public User (int id, String name, String username, String email, String avatar) {
        this.id = id;
        this.name = name;
        this.username = username;
        this.email = email;
        this.avatar = avatar;
    }

    public int getId() {
        return id;
    }

    public String getName() {
        return name;
    }

    public String getUsername() {
        return username;
    }

    public String getEmail() {
        return email;
    }

    public String getAvatar() {
        return avatar;
    }
}
