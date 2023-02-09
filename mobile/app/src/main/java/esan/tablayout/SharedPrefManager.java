package esan.tablayout;

import android.content.Context;
import android.content.SharedPreferences;

import java.util.Map;

import esan.tablayout.Model.User;

public class SharedPrefManager {

    private static final String SHARED_PREF_NAME = "sharedpref";
    private static final String KEY_NAME = "keyname";
    private static final String KEY_USERNAME = "keyusername";
    private static final String KEY_EMAIL = "keyemail";
    private static final String KEY_ID = "keyid";
    private static final String KEY_AVATAR = "keyavatar";

    private static final String KEY_HOUSE = "house";

    private static final String KEY_NOTIFICATION = "notification";

    private static final String KEY_STATISTICS = "statistics";

    private static SharedPrefManager mInstance;
    private static Context mCtx;

    private SharedPrefManager (Context context) {
        mCtx = context;
    }

    public static synchronized SharedPrefManager getInstance(Context context) {
        if (mInstance == null) {
            mInstance = new SharedPrefManager(context);
        }
        return mInstance;
    }

    public void houseArray (String s) {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(KEY_HOUSE, s);
        editor.commit();

    }

    public void notificationArray (String s) {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(KEY_NOTIFICATION, s);
        editor.commit();

    }

    public void statisticsArray(String s) {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(KEY_STATISTICS, s);
        editor.commit();
    }

    //this method will give the logged in user house
    public String getHouse() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        String json = sharedPreferences.getString(KEY_HOUSE,null);
        return json;
    }

    //this method will give the logged in user notifications
    public String getNotification() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        String json = sharedPreferences.getString(KEY_NOTIFICATION,null);
        return json;
    }

    //this method will give the logged in user statistics
    public String getStatistics() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        String json = sharedPreferences.getString(KEY_STATISTICS,null);
        return json;
    }

    //this method will store the house data in shared preferences
    public void userLogin (User user) {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt(KEY_ID, user.getId());
        editor.putString(KEY_NAME, user.getName());
        editor.putString(KEY_USERNAME, user.getUsername());
        editor.putString(KEY_EMAIL, user.getEmail());
        editor.putString(KEY_AVATAR, user.getAvatar());
        editor.apply();
    }


    //this method will check wether user is logged in or not
    public boolean isLoggedIn() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString(KEY_USERNAME, null) != null;
    }

    //this method will give the logged in user
    public User getUser() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        return new User(
            sharedPreferences.getInt(KEY_ID, -1),
            sharedPreferences.getString(KEY_NAME, null),
            sharedPreferences.getString(KEY_USERNAME, null),
            sharedPreferences.getString(KEY_EMAIL, null),
            sharedPreferences.getString(KEY_AVATAR, null)
        );
    }

    //this method will logout the user
    public void logout() {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.clear();
        editor.apply();
    }

    //this method will delete a key
    public void deleteKey(String i) {
        SharedPreferences sharedPreferences = mCtx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();

        Map<String, ?> allEntries = sharedPreferences.getAll();
        for (Map.Entry<String, ?> entry : allEntries.entrySet()) {
            String key = entry.getKey();
            if (key.contains(i)) {
                editor.remove(i);
            }
            editor.commit();
        }
    }
}
