package esan.tablayout.Fragment;

import static android.app.Activity.RESULT_OK;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.MediaStore;
import android.provider.OpenableColumns;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.TranslateAnimation;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.ItemTouchHelper;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.ImageRequest;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import de.hdodenhof.circleimageview.CircleImageView;
import esan.tablayout.Adapter.NotificationAdapter;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.Login;
import esan.tablayout.Model.Notification;
import esan.tablayout.Model.User;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class Settings extends Fragment {

    TextInputLayout name, username, email;
    CircleImageView avatar;
    Button logout_btn, changePic, removePic, changeName, changeUsername, changeEmail;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        return inflater.inflate(R.layout.settings, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        changePic = getView().findViewById(R.id.btnChange);
        removePic = getView().findViewById(R.id.btnRemove);
        changeName = getView().findViewById(R.id.btnName);
        changeUsername = getView().findViewById(R.id.btnUsername);
        changeEmail = getView().findViewById(R.id.btnEmail);

        name = getView().findViewById(R.id.text_name);
        String getName = SharedPrefManager.getInstance(getContext()).getUser().getName();
        String capitalizedName = (getName.substring(0, 1).toUpperCase() + getName.substring(1)).trim();
        name.getEditText().setText(capitalizedName);

        username = getView().findViewById(R.id.text_username);
        username.getEditText().setText(SharedPrefManager.getInstance(getContext()).getUser().getUsername());

        email = getView().findViewById(R.id.text_email);
        email.getEditText().setText(SharedPrefManager.getInstance(getContext()).getUser().getEmail());

        avatar = getView().findViewById(R.id.userAvatar);
        String getAvatar = SharedPrefManager.getInstance(getContext()).getUser().getAvatar();

        // Create a RequestQueue
        RequestQueue queue = Volley.newRequestQueue(getContext());

        // Create an ImageRequest to load the image from the server
        ImageRequest request = new ImageRequest("https://esan-tesp-ds-paw.web.ua.pt/tesp-ds-g10/uploads/avatar/" + getAvatar,
                new Response.Listener<Bitmap>() {
                    @Override
                    public void onResponse(Bitmap bitmap) {
                        // Set the image in the ImageView
                        avatar.setImageBitmap(bitmap);
                    }
                },
                0, 0, ImageView.ScaleType.CENTER_CROP, Bitmap.Config.RGB_565,
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        // Handle errors
                        Toast.makeText(getContext(), "Error loading image: " + error.getMessage(), Toast.LENGTH_LONG).show();
                    }
                }
        );
        // Add the request to the RequestQueue
        queue.add(request);

        logout_btn = view.findViewById(R.id.logout);

        logout_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                SharedPrefManager.getInstance(getContext().getApplicationContext()).logout();
                Toast.makeText(getContext(), getResources().getString(R.string.logout_message), Toast.LENGTH_SHORT).show();
                startActivity(new Intent(getContext(), Login.class));
            }
        });

        changePic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                imageChooser();
            }
        });

        removePic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

            }
        });

        changeName.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                change("name");
            }
        });

        changeUsername.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                change("username");
            }
        });

        changeEmail.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                change("email");
            }
        });
    }

    void imageChooser() {

        // create an instance of the
        // intent of the type image
        Intent i = new Intent();
        i.setType("image/*");
        i.setAction(Intent.ACTION_GET_CONTENT);

        // pass the constant to compare it
        // with the returned requestCode
        startActivityForResult(Intent.createChooser(i, "Select Picture"), 200);
    }

    // this function is triggered when user
    // selects the image from the imageChooser
    @SuppressLint("Range")
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode == RESULT_OK) {

            // compare the resultCode with the
            // SELECT_PICTURE constant
            if (requestCode == 200) {
                // Get the url of the image from data
                Uri selectedImageUri = data.getData();
                if (null != selectedImageUri) {
                    // update the preview image in the layout
                    String result = null;
                    if (selectedImageUri.getScheme().equals("content")) {
                        Cursor cursor = getContext().getContentResolver().query(selectedImageUri, null, null, null, null);
                        try {
                            if (cursor != null && cursor.moveToFirst()) {
                                result = cursor.getString(cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME));
                            }
                        } finally {
                            cursor.close();
                        }
                    }
                    if (result == null) {
                        result = selectedImageUri.getPath();
                        int cut = result.lastIndexOf('/');
                        if (cut != -1) {
                            result = result.substring(cut + 1);
                        }
                    }
                    avatar.setImageURI(selectedImageUri);

                    String fileName = String.valueOf(selectedImageUri);
                    HttpURLConnection conn = null;
                    DataOutputStream dos = null;
                    String lineEnd = "\r\n";
                    String twoHyphens = "--";
                    String boundary = "*****";
                    int bytesRead, bytesAvailable, bufferSize;
                    byte[] buffer;
                    int maxBufferSize = 1 * 1024 * 1024;
                    File sourceFile = new File(selectedImageUri.getPath());

                    if (!sourceFile.isFile()) {

                        Log.e("uploadFile", "Source File not exist :" + result);

                    } else {
                        try {

                            // open a URL connection to the Servlet
                            FileInputStream fileInputStream = new FileInputStream(sourceFile);
                            URL url = new URL(URLS.URL_UPLOADFILE);

                            // Open a HTTP  connection to  the URL
                            conn = (HttpURLConnection) url.openConnection();
                            conn.setDoInput(true); // Allow Inputs
                            conn.setDoOutput(true); // Allow Outputs
                            conn.setUseCaches(false); // Don't use a Cached Copy
                            conn.setRequestMethod("POST");
                            conn.setRequestProperty("Connection", "Keep-Alive");
                            conn.setRequestProperty("ENCTYPE", "multipart/form-data");
                            conn.setRequestProperty("Content-Type", "multipart/form-data;boundary=" + boundary);
                            conn.setRequestProperty("fileToUpload", fileName);

                            dos = new DataOutputStream(conn.getOutputStream());

                            dos.writeBytes(twoHyphens + boundary + lineEnd);
                            dos.writeBytes("Content-Disposition: form-data; name=\"fileToUpload\";filename=\""
                                    + fileName + "\"" + lineEnd);

                            dos.writeBytes(lineEnd);

                            // create a buffer of  maximum size
                            bytesAvailable = fileInputStream.available();

                            bufferSize = Math.min(bytesAvailable, maxBufferSize);
                            buffer = new byte[bufferSize];

                            // read file and write it into form...
                            bytesRead = fileInputStream.read(buffer, 0, bufferSize);

                            while (bytesRead > 0) {

                                dos.write(buffer, 0, bufferSize);
                                bytesAvailable = fileInputStream.available();
                                bufferSize = Math.min(bytesAvailable, maxBufferSize);
                                bytesRead = fileInputStream.read(buffer, 0, bufferSize);

                            }

                            // send multipart form data necesssary after file data...
                            dos.writeBytes(lineEnd);
                            dos.writeBytes(twoHyphens + boundary + twoHyphens + lineEnd);

                            // Responses from the server (code and message)
                            int serverResponseCode = conn.getResponseCode();
                            String serverResponseMessage = conn.getResponseMessage();

                            Log.i("uploadFile", "HTTP Response is : "
                                    + serverResponseMessage + ": " + serverResponseCode);


                            //close the streams //
                            fileInputStream.close();
                            dos.flush();
                            dos.close();

                        } catch (MalformedURLException ex) {

                            ex.printStackTrace();

                            Log.e("Upload file to server", "error: " + ex.getMessage(), ex);
                        } catch (Exception e) {

                            e.printStackTrace();
                        }
                    } // End else block

                    //uploadImage(result);
                }
            }
        }
    }


    private void uploadImage(String imageName) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_UPLOAD,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {

                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(getContext(), "Error Occurred", Toast.LENGTH_LONG).show();
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("userid", user_id);
                params.put("imagem", imageName);

                return params;
            }
        };

        queue.add(stringRequest);
    }

    private void change(String option) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        final String textName = name.getEditText().getText().toString();
        final String textEmail = email.getEditText().getText().toString();
        final String textUsername = username.getEditText().getText().toString();

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_CHANGE,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        reloadUser();
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(getContext(), "Error Occurred", Toast.LENGTH_LONG).show();
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("userid", user_id);
                if (option == "name") {
                    params.put("name", textName);
                } else if (option == "email") {
                    params.put("email", textEmail);
                } else if (option == "username") {
                    params.put("username", textUsername);
                }

                return params;
            }
        };

        queue.add(stringRequest);
    }

    private void reloadUser() {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_LOGIN,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            //getting the user from the response
                            //Somethings wrong with json, need to fix
                            JSONObject userJson = jsonObject.getJSONObject("user");

                            //creating a new user object
                            User user = new User(
                                    userJson.getInt("id"),
                                    userJson.getString("name"),
                                    userJson.getString("username"),
                                    userJson.getString("email"),
                                    userJson.getString("avatar")
                            );

                            //storing the user in shared preferences
                            SharedPrefManager.getInstance(getContext()).userLogin(user);

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(getContext(), "Error Occurred", Toast.LENGTH_LONG).show();
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