package esan.tablayout;

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

import androidx.appcompat.app.AppCompatActivity;

import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;

import esan.tablayout.Model.User;

public class SignUp extends AppCompatActivity {

    Button callLogin, signup_btn;
    ImageView image;
    TextView logoText, sloganText;
    TextInputLayout name, username, email, password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);

        setContentView(R.layout.activity_sign_up);

        //if the user is already logged in we will directly start the dashboard activity
        if (SharedPrefManager.getInstance(this).isLoggedIn()) {
            finish();
            startActivity(new Intent(this, Dashboard.class));
            return;
        }

        //Hooks
        callLogin = findViewById(R.id.login_screen);
        image = findViewById(R.id.logo_image);
        logoText = findViewById(R.id.logo_name);
        sloganText = findViewById(R.id.slogan_name);
        name = findViewById(R.id.name);
        username = findViewById(R.id.username);
        email = findViewById(R.id.email);
        password = findViewById(R.id.password);
        signup_btn = findViewById(R.id.signup_btn);


        callLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(SignUp.this, Login.class);

                Pair[] pairs = new Pair[9];

                pairs[0] = new Pair<View, String>(image, "logo_image");
                pairs[1] = new Pair<View, String>(logoText, "logo_text");
                pairs[2] = new Pair<View, String>(sloganText, "logo_desc");
                pairs[3] = new Pair<View, String>(name, "name_tran");
                pairs[4] = new Pair<View, String>(username, "username_tran");
                pairs[5] = new Pair<View, String>(email, "email_tran");
                pairs[6] = new Pair<View, String>(password, "password_tran");
                pairs[7] = new Pair<View, String>(signup_btn, "button_tran");
                pairs[8] = new Pair<View, String>(callLogin, "login_signup_tran");

                ActivityOptions options = ActivityOptions.makeSceneTransitionAnimation(SignUp.this, pairs);
                startActivity(intent, options.toBundle());
            }
        });

        signup_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //if user pressed on button register
                //here we will register the user to server
                registerUser();
            }
        });
    }


    private void registerUser() {
        final String textName = name.getEditText().getText().toString().trim();
        final String textUsername = username.getEditText().getText().toString().trim();
        final String textEmail = email.getEditText().getText().toString().trim();
        final String textPassword = password.getEditText().getText().toString().trim();

        //first we will do the validations

        if (TextUtils.isEmpty(textName)) {
            name.setError(getResources().getString(R.string.error_name));
            name.requestFocus();
            return;
        }

        if (TextUtils.isEmpty(textUsername)) {
            username.setError(getResources().getString(R.string.error_username));
            username.requestFocus();
            return;
        }

        if (TextUtils.isEmpty(textEmail)) {
            email.setError(getResources().getString(R.string.error_email_empty));
            email.requestFocus();
            return;
        }

        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(textEmail).matches()) {
            email.setError(getResources().getString(R.string.error_email_invalid));
            email.requestFocus();
            return;
        }

        if (TextUtils.isEmpty(textPassword)) {
            password.setError(getResources().getString(R.string.error_password));
            password.requestFocus();
            return;
        }

        //if it passes all the validations
        class RegisterUser extends AsyncTask<Void, Void, String> {

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("name", textName);
                params.put("username", textUsername);
                params.put("email", textEmail);
                params.put("password", textPassword);

                //returing the response
                return requestHandler.sendPostRequest(URLS.URL_REGISTER, params);
            }

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

                        //starting the profile activity
                        finish();
                        startActivity(new Intent(getApplicationContext(), Dashboard.class));
                    } else {
                        Toast.makeText(getApplicationContext(), "Some error occurred", Toast.LENGTH_SHORT).show();
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }

        }

        //executing the async task
        RegisterUser ru = new RegisterUser();
        ru.execute();
    }
}