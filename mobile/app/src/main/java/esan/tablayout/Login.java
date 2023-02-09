package esan.tablayout;

import androidx.appcompat.app.AppCompatActivity;

import android.app.ActivityOptions;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Pair;
import android.view.View;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.Model.User;

public class Login extends AppCompatActivity {

    Button callSignUp, login_btn;
    ImageView image;
    TextView logoText, sloganText;
    TextInputLayout username, password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);

        setContentView(R.layout.activity_login);

        //Hooks
        callSignUp = findViewById(R.id.signup_screen);
        image = findViewById(R.id.logo_image);
        logoText = findViewById(R.id.logo_name);
        sloganText = findViewById(R.id.slogan_name);
        username = findViewById(R.id.username);
        password = findViewById(R.id.password);
        login_btn = findViewById(R.id.login_btn);

        callSignUp.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(Login.this, SignUp.class);

                Pair[] pairs = new Pair[7];

                pairs[0] = new Pair<View, String>(image, "logo_image");
                pairs[1] = new Pair<View, String>(logoText, "logo_text");
                pairs[2] = new Pair<View, String>(sloganText, "logo_desc");
                pairs[3] = new Pair<View, String>(username, "username_tran");
                pairs[4] = new Pair<View, String>(password, "password_tran");
                pairs[5] = new Pair<View, String>(login_btn, "button_tran");
                pairs[6] = new Pair<View, String>(callSignUp, "login_signup_tran");

                ActivityOptions options = ActivityOptions.makeSceneTransitionAnimation(Login.this, pairs);
                startActivity(intent, options.toBundle());
            }
        });

        //if user presses on login
        //calling the method login
        login_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                userLogin();
            }
        });
    }

    private void userLogin() {
        //first getting the values
        final String textUsername = username.getEditText().getText().toString();
        final String textPassword = password.getEditText().getText().toString();

        //validating inputs
        if (TextUtils.isEmpty(textUsername)) {
            username.setError(getResources().getString(R.string.error_username));
            username.requestFocus();
            return;
        }

        if (TextUtils.isEmpty(textPassword)) {
            password.setError(getResources().getString(R.string.error_password));
            password.requestFocus();
            return;
        }

        //if everything is fine
        class UserLogin extends AsyncTask<Void, Void, String> {

            @Override
            protected void onPostExecute(String s) {
                super.onPostExecute(s);

                try {

                    //converting response to json object
                    JSONObject obj = new JSONObject(s);

                    //if no error in response
                    if (!obj.getBoolean("error")) {

                        Toast.makeText(getApplicationContext(), getResources().getString(R.string.login_message), Toast.LENGTH_SHORT).show();
                        //startActivity(new Intent(getApplicationContext(), Dashboard.class));

                        //getting the user from the response
                        //Somethings wrong with json, need to fix
                        JSONObject userJson = obj.getJSONObject("user");

                        //creating a new user object
                        User user = new User(
                                userJson.getInt("id"),
                                userJson.getString("name"),
                                userJson.getString("username"),
                                userJson.getString("email"),
                                userJson.getString("avatar")
                        );

                        //storing the user in shared preferences
                        SharedPrefManager.getInstance(getApplicationContext()).userLogin(user);

                        loadHouse(new VolleyCallBack() {
                            @Override
                            public void onSuccess() {
                                loadNotifications(new VolleyCallBack() {
                                    @Override
                                    public void onSuccess() {
                                        loadStatistics(new VolleyCallBack() {
                                            @Override
                                            public void onSuccess() {
                                                //starting the profile activity
                                                startActivity(new Intent(getApplicationContext(), Dashboard.class));
                                                finish();
                                            }
                                        });
                                    }
                                });
                            }
                        });

                    } else {
                        Toast.makeText(getApplicationContext(), getResources().getString(R.string.error_login), Toast.LENGTH_SHORT).show();
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("username", textUsername);
                params.put("password", textPassword);

                //returning the response
                return requestHandler.sendPostRequest(URLS.URL_LOGIN, params);
            }
        }

        UserLogin ul = new UserLogin();
        ul.execute();
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