package esan.tablayout.Model;

public class Notification {

    private int ID;
    private String Icon;
    private String Time;
    private String Date;
    private String Title;
    private String Description;

    public Notification() {

    }

    public Notification(int ID, String icon, String time, String date, String title, String description) {
        this.ID = ID;
        Icon = icon;
        Time = time;
        Date = date;
        Title = title;
        Description = description;
    }

    public int getID() {
        return ID;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public String getIcon() {
        return Icon;
    }

    public void setIcon(String icon) {
        Icon = icon;
    }

    public String getTime() {
        return Time;
    }

    public void setTime(String time) {
        Time = time;
    }

    public String getDate() {
        return Date;
    }

    public void setDate(String date) {
        Date = date;
    }

    public String getTitle() {
        return Title;
    }

    public void setTitle(String title) {
        Title = title;
    }

    public String getDescription() {
        return Description;
    }

    public void setDescription(String description) {
        Description = description;
    }
}

