package esan.tablayout.Model;

public class Room {

    private int ID;
    private String Name;

    public Room(int ID, String name) {
        this.ID = ID;
        Name = name;
    }

    public int getID() {
        return ID;
    }

    public String getName() {
        return Name;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public void setName(String name) {
        Name = name;
    }
}

