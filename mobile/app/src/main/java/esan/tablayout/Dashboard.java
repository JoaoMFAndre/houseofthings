package esan.tablayout;

import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;

import android.os.Bundle;
import android.view.View;
import android.view.WindowManager;

import com.google.android.material.bottomnavigation.BottomNavigationView;

import esan.tablayout.Fragment.Add_Device;
import esan.tablayout.Fragment.Consumption;
import esan.tablayout.Fragment.DeviceMenu;
import esan.tablayout.Fragment.Homepage;
import esan.tablayout.Fragment.Notifications;
import esan.tablayout.Fragment.Settings;

public class Dashboard extends AppCompatActivity {


    Fragment f1, f2, f3, f4, f5;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.dashboard);

        f1 = new Homepage();
        f2 = new Consumption();
        f3 = new DeviceMenu();
        f4 = new Notifications();
        f5 = new Settings();

        BottomNavigationView navigation = findViewById(R.id.navegacao);
        navigation.setOnItemSelectedListener(item -> {
            switch (item.getItemId()) {
                case R.id.nav_1:
                    getSupportFragmentManager()
                            .beginTransaction()
                            .replace(R.id.placeholder, f1)
                            .commit();
                    break;
                case R.id.nav_2:
                    getSupportFragmentManager()
                            .beginTransaction()
                            .replace(R.id.placeholder, f2)
                            .commit();
                    break;
                case R.id.nav_3:
                    getSupportFragmentManager()
                            .beginTransaction()
                            .replace(R.id.placeholder, f3)
                            .commit();
                    break;
                case R.id.nav_4:
                    getSupportFragmentManager()
                            .beginTransaction()
                            .replace(R.id.placeholder, f4)
                            .commit();
                    break;
                case R.id.nav_5:
                    getSupportFragmentManager()
                            .beginTransaction()
                            .replace(R.id.placeholder, f5)
                            .commit();
                    break;
            }
            return true;
        });
    }
}