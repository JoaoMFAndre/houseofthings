package esan.tablayout.Model;

public class Statistics {

    private int ID;
    private int Day;
    private String Month;
    private int Year;
    private int Consumption;
    private int rID;
    private String rName;
    private int Total;

    public Statistics(int ID, int day, String month, int year, int consumption, int rID, int total, String rname) {
        this.ID = ID;
        Day = day;
        Month = month;
        Year = year;
        Consumption = consumption;
        this.rID = rID;
        Total = total;
        rName = rname;
    }

    public String getrName() {
        return rName;
    }

    public void setrName(String rName) {
        this.rName = rName;
    }

    public int getID() {
        return ID;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public int getDay() {
        return Day;
    }

    public void setDay(int day) {
        Day = day;
    }

    public String getMonth() {
        return Month;
    }

    public void setMonth(String month) {
        Month = month;
    }

    public int getYear() {
        return Year;
    }

    public void setYear(int year) {
        Year = year;
    }

    public int getConsumption() {
        return Consumption;
    }

    public void setConsumption(int consumption) {
        Consumption = consumption;
    }

    public int getrID() {
        return rID;
    }

    public void setrID(int rID) {
        this.rID = rID;
    }

    public int getTotal() {
        return Total;
    }

    public void setTotal(int total) {
        Total = total;
    }
}
