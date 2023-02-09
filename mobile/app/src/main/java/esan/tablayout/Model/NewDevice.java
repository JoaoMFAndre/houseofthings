package esan.tablayout.Model;

public class NewDevice {

    private int ID;
    private String Ip;
    private String Mac;

    public NewDevice() {

    }

    public NewDevice(int ID, String ip, String mac) {
        this.ID = ID;
        Ip = ip;
        Mac = mac;
    }

    public int getID() {
        return ID;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public String getIp() {
        return Ip;
    }

    public void setIp(String ip) {
        Ip = ip;
    }

    public String getMac() {
        return Mac;
    }

    public void setMac(String mac) {
        Mac = mac;
    }
}
