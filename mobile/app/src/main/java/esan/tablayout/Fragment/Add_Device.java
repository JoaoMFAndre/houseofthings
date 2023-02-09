package esan.tablayout.Fragment;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Adapter.AddDeviceAdapter;
import esan.tablayout.Adapter.AddIconAdapter;
import esan.tablayout.Adapter.AddRoomAdapter;
import esan.tablayout.Dashboard;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.MainActivity;
import esan.tablayout.Model.NewDevice;
import esan.tablayout.Model.Room;
import esan.tablayout.Model.User;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class Add_Device extends Fragment {

    private ArrayList<Room> listRoom;
    private ArrayList<NewDevice> listNewDevice;
    private ArrayList<Integer> listIcons;
    RecyclerView recyclerViewRoom, recyclerViewDevice, recyclerViewIcon;
    private SwipeRefreshLayout swipeRefreshLayout;

    TextInputLayout textRoom, textDevice;
    Button createBtn;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.add_device, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        textRoom = view.findViewById(R.id.text_room_name);
        textDevice = view.findViewById(R.id.text_device_name);

        listRoom = new ArrayList<>();
        listNewDevice = new ArrayList<>();
        listIcons  = new ArrayList<>();

        recyclerViewRoom = view.findViewById(R.id.recycler_room);
        recyclerViewDevice = view.findViewById(R.id.recycler_device);
        recyclerViewIcon = view.findViewById(R.id.recycler_icon);

        LinearLayoutManager linearLayoutManagerRoom = new LinearLayoutManager(getContext());
        linearLayoutManagerRoom.setOrientation(linearLayoutManagerRoom.HORIZONTAL);
        LinearLayoutManager linearLayoutManagerDevice = new LinearLayoutManager(getContext());
        linearLayoutManagerDevice.setOrientation(linearLayoutManagerDevice.HORIZONTAL);
        LinearLayoutManager linearLayoutManagerIcon = new LinearLayoutManager(getContext());
        linearLayoutManagerIcon.setOrientation(linearLayoutManagerIcon.HORIZONTAL);

        recyclerViewRoom.setLayoutManager(linearLayoutManagerRoom);
        recyclerViewDevice.setLayoutManager(linearLayoutManagerDevice);
        recyclerViewIcon.setLayoutManager(linearLayoutManagerIcon);

        createFields();

        swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

        // SetOnRefreshListener on SwipeRefreshLayout
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {

                // Add Fragment
                if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {
                    listRoom.clear();
                    listNewDevice.clear();
                    listIcons.clear();
                    createFields();
                }

                swipeRefreshLayout.setRefreshing(false);
            }
        });

        createBtn = view.findViewById(R.id.button);

        createBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                createDevice();
            }
        });
    }

    private void createDevice() {

        //first getting the values
        final String textDeviceName = textDevice.getEditText().getText().toString();
        final String textRoomName = textRoom.getEditText().getText().toString();

        final String deviceID = AddDeviceAdapter.deviceID;
        final String roomID = AddRoomAdapter.roomID;
        final String iconName = AddIconAdapter.iconName.substring(3) + ".svg";

        final String uid = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        //validating inputs
        if (TextUtils.isEmpty(textDeviceName)) {
            textDevice.setError(getResources().getString(R.string.error_device_name));
            textDevice.requestFocus();
            return;
        }

        if (TextUtils.isEmpty(roomID)) {
            if (TextUtils.isEmpty(textRoomName)) {
                textRoom.setError(getResources().getString(R.string.error_device_name));
                textRoom.requestFocus();
                return;
            }
        }

        //if everything is fine
        class CreateDevice extends AsyncTask<Void, Void, String> {

            @Override
            protected void onPostExecute(String s) {
                super.onPostExecute(s);

                loadHouse(new VolleyCallBack() {
                    @Override
                    public void onSuccess() {
                        loadNotifications(new VolleyCallBack() {
                            @Override
                            public void onSuccess() {
                                loadStatistics(new VolleyCallBack() {
                                    @Override
                                    public void onSuccess() {
                                        Toast.makeText(getContext(), getResources().getString(R.string.create_message), Toast.LENGTH_SHORT).show();
                                        listRoom.clear();
                                        listNewDevice.clear();
                                        listIcons.clear();
                                        createFields();
                                    }
                                });
                            }
                        });
                    }
                });
            }

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();

                params.put("userid", uid);
                params.put("deviceName", textDeviceName);
                if (!TextUtils.isEmpty(roomID)) {
                    params.put("roomID", roomID);
                } else {
                    params.put("roomName", textRoomName);
                }
                params.put("deviceID", deviceID);
                params.put("iconName", iconName);

                //returning the response
                return requestHandler.sendPostRequest(URLS.URL_CREATE, params);
            }
        }

        CreateDevice cd = new CreateDevice();
        cd.execute();
    }

    private void loadHouse(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_HOUSE,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getContext()).houseArray(jsonObject.toString());

                            callBack.onSuccess();

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
        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                3000,
                2,
                2));
        queue.add(stringRequest);
    }
    private void loadNotifications(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_NOTIFICATION,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getContext()).notificationArray(jsonObject.toString());

                            callBack.onSuccess();

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

        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                3000,
                2,
                2));
        queue.add(stringRequest);
    }
    private void loadStatistics(final VolleyCallBack callBack) {

        String user_id = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        RequestQueue queue = Volley.newRequestQueue(getContext());
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                URLS.URL_STATISTICS,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {

                            //Converting the string to json array object
                            JSONObject jsonObject = new JSONObject(response);
                            SharedPrefManager.getInstance(getContext()).statisticsArray(jsonObject.toString());

                            callBack.onSuccess();

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

    private void createFields() {
        listRoom.add(new Room(0, "add"));
        if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {

            try {

                String json = SharedPrefManager.getInstance(getContext()).getHouse();
                JSONObject jsonObject = new JSONObject(json);
                JSONArray jsonArray = jsonObject.getJSONArray("room");
                if (jsonArray != null) {

                    //Traversing through all the object
                    for (int i = 0; i < jsonArray.length(); i++) {
                        //Getting House object from json array
                        JSONObject room = jsonArray.getJSONObject(i);

                        listRoom.add(new Room(
                                room.getInt("id"),
                                room.getString("name")
                        ));
                    }
                    //Populating House with the returned rooms and devices
                    AddRoomAdapter adapterRoom = new AddRoomAdapter(listRoom);
                    recyclerViewRoom.setAdapter(adapterRoom);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

        if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {

            try {

                String json = SharedPrefManager.getInstance(getContext()).getHouse();
                JSONObject jsonObject = new JSONObject(json);
                JSONArray jsonArray = jsonObject.getJSONArray("new_device");

                if (jsonArray != null) {
                    //Traversing through all the object
                    for (int i = 0; i < jsonArray.length(); i++) {
                        //Getting House object from json array
                        JSONObject new_device = jsonArray.getJSONObject(i);

                        listNewDevice.add(new NewDevice(
                                new_device.getInt("id"),
                                new_device.getString("ip"),
                                new_device.getString("mac")
                        ));
                    }
                    //Populating House with the returned rooms and devices
                    AddDeviceAdapter adapterDevice = new AddDeviceAdapter(listNewDevice);
                    recyclerViewDevice.setAdapter(adapterDevice);

                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

        listIcons.add(R.drawable.ic_bulb);
        listIcons.add(R.drawable.ic_ac);
        listIcons.add(R.drawable.ic_thermostat);

        AddIconAdapter adapterIcon = new AddIconAdapter(listIcons);
        recyclerViewIcon.setAdapter(adapterIcon);
    }
}