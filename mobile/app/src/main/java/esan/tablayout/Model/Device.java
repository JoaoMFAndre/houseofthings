package esan.tablayout.Model;

public class Device {

    private String Icon;
    private int ID;
    private int dConsumption;
    private int Input;
    private int Output;
    //private int Humidity;
    private int Brightness;

    //private double Temperature;

    private String Name;
    private String State;
    private String Type;

    public Device() {

    }

    public Device(String icon, int ID, int dConsumption, int input, int output, int brightness, String name, String state, String type) {
        Icon = icon;
        this.ID = ID;
        this.dConsumption = dConsumption;
        Input = input;
        Output = output;
        //Humidity = humidity;
        Brightness = brightness;
        //Temperature = temperature;
        Name = name;
        State = state;
        Type = type;
    }

    // Getter

    public String getName() {
        return Name;
    }

    public String getIcon() {
        return Icon;
    }

    public int getID() {
        return ID;
    }

    public int getdConsumption() {
        return dConsumption;
    }

    public int getInput() {
        return Input;
    }

    public int getOutput() {
        return Output;
    }

    /*public int getHumidity() {
        return Humidity;
    }*/

    public int getBrightness() {
        return Brightness;
    }

    /*public double getTemperature() {
        return Temperature;
    }*/

    public String getState() {
        return State;
    }

    public String getType() {
        return Type;
    }

    //Setter

    public void setName(String name) {
        Name = name;
    }

    public void setIcon(String icon) {
        Icon = icon;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public void setdConsumption(int dConsumption) {
        this.dConsumption = dConsumption;
    }

    public void setInput(int input) {
        Input = input;
    }

    public void setOutput(int output) {
        Output = output;
    }

    /*public void setHumidity(int humidity) {
        Humidity = humidity;
    }*/

    public void setBrightness(int brightness) {
        Brightness = brightness;
    }

    /*public void setTemperature(double temperature) {
        Temperature = temperature;
    }*/

    public void setState(String state) {
        State = state;
    }

    public void setType(String type) {
        Type = type;
    }
}
