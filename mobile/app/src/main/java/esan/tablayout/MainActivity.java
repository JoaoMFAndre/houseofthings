package esan.tablayout;

import androidx.appcompat.app.AppCompatActivity;

import android.app.ActivityOptions;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Pair;
import android.view.View;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Interface.VolleyCallBack;

public class MainActivity extends AppCompatActivity {

    private static int SPLASH_SCREEN = 3000;

    //Variables
    Animation topAnim, bottomAnim;
    ImageView image;
    TextView logo, slogan;

    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.activity_main);

        //Animations
        topAnim = AnimationUtils.loadAnimation(this, R.anim.top_animation);
        bottomAnim = AnimationUtils.loadAnimation(this, R.anim.bottom_animation);

        //Hooks
        image = findViewById(R.id.imageView);
        logo = findViewById(R.id.textView9);
        slogan = findViewById(R.id.textView10);

        image.setAnimation(topAnim);
        logo.setAnimation(bottomAnim);
        slogan.setAnimation(bottomAnim);


        new Handler().postDelayed(new Runnable() {
            @Override

            public void run() {
                if (SharedPrefManager.getInstance(getApplicationContext()).isLoggedIn()) {

                    loadHouse(new VolleyCallBack() {
                        @Override
                        public void onSuccess() {
                            loadNotifications(new VolleyCallBack() {
                                @Override
                                public void onSuccess() {
                                    loadStatistics(new VolleyCallBack() {
                                        @Override
                                        public void onSuccess() {
                                            Intent intent = new Intent(MainActivity.this, Dashboard.class);
                                            startActivity(intent);
                                        }
                                    });
                                }
                            });
                        }
                    });


                } else {
                    Intent intent = new Intent(MainActivity.this, Login.class);

                    Pair[] pairs = new Pair[2];
                    pairs[0] = new Pair<View, String>(image, "logo_image");
                    pairs[1] = new Pair<View, String>(logo, "logo_text");

                    ActivityOptions options = ActivityOptions.makeSceneTransitionAnimation(MainActivity.this, pairs);
                    startActivity(intent, options.toBundle());
                }
            }
        }, SPLASH_SCREEN);
    }

    private void loadHouse(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getApplicationContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getApplicationContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_HOUSE,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getApplicationContext()).houseArray(jsonObject.toString());

                            callBack.onSuccess();

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(MainActivity.this, "Error Occurred", Toast.LENGTH_LONG).show();
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("user_id", user_id);

                return params;
            }
        };
        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                3000,
                2,
                2));
        queue.add(stringRequest);
    }
    private void loadNotifications(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getApplicationContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getApplicationContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_NOTIFICATION,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getApplicationContext()).notificationArray(jsonObject.toString());

                            callBack.onSuccess();

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(MainActivity.this, "Error Occurred", Toast.LENGTH_LONG).show();
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("user_id", user_id);

                return params;
            }
        };

        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                3000,
                2,
                2));
        queue.add(stringRequest);
    }
    private void loadStatistics(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getApplicationContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getApplicationContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_STATISTICS,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getApplicationContext()).statisticsArray(jsonObject.toString());

                            callBack.onSuccess();

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(getApplicationContext(), "Error Occurred", Toast.LENGTH_LONG).show();
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("user_id", user_id);

                return params;
            }
        };

        queue.add(stringRequest);
    }
}