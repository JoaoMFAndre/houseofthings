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
import esan.tablayout.Adapter.RemoveDeviceAdapter;
import esan.tablayout.Dashboard;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.MainActivity;
import esan.tablayout.Model.Device;
import esan.tablayout.Model.NewDevice;
import esan.tablayout.Model.Room;
import esan.tablayout.Model.User;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class Remove_Device extends Fragment {

    private ArrayList<Device> listDevice;
    RecyclerView recyclerViewDevice;
    private SwipeRefreshLayout swipeRefreshLayout;

    Button removeBtn;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.remove_device, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        listDevice = new ArrayList<>();

        recyclerViewDevice = view.findViewById(R.id.recycler_device);

        LinearLayoutManager linearLayoutManagerDevice = new LinearLayoutManager(getContext());
        linearLayoutManagerDevice.setOrientation(linearLayoutManagerDevice.HORIZONTAL);

        recyclerViewDevice.setLayoutManager(linearLayoutManagerDevice);

        createFields();

        swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

        // SetOnRefreshListener on SwipeRefreshLayout
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {

                // Add Fragment
                if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {
                    listDevice.clear();
                    createFields();
                }

                swipeRefreshLayout.setRefreshing(false);
            }
        });

        removeBtn = view.findViewById(R.id.button);

        removeBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                removeDevice();
            }
        });
    }

    private void removeDevice() {

        final String deviceID = RemoveDeviceAdapter.deviceID;

        final String uid = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        //if everything is fine
        class RemoveDevice extends AsyncTask<Void, Void, String> {

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
                                        Toast.makeText(getContext(), getResources().getString(R.string.remove_message), Toast.LENGTH_SHORT).show();
                                        listDevice.clear();
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
                params.put("deviceID", deviceID);

                //returning the response
                return requestHandler.sendPostRequest(URLS.URL_REMOVE, params);
            }
        }

        RemoveDevice rd = new RemoveDevice();
        rd.execute();
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

        if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {

            try {

                String json = SharedPrefManager.getInstance(getContext()).getHouse();
                JSONObject jsonObject = new JSONObject(json);
                JSONArray jsonArray = jsonObject.getJSONArray("device");

                if (jsonArray != null) {
                    //Traversing through all the object
                    for (int i = 0; i < jsonArray.length(); i++) {
                        //Getting House object from json array
                        JSONObject house = jsonArray.getJSONObject(i);

                            //Populating House with the returned rooms and devices
                            listDevice.add(new Device(
                                    house.getString("icon"),
                                    house.getInt("device_id"),
                                    house.getInt("consumption"),
                                    house.getInt("input"),
                                    house.getInt("output"),
                                    house.getInt("brightness"),
                                    house.getString("device_name"),
                                    house.getString("state"),
                                    house.getString("type")
                            ));
                        }

                    //Populating House with the returned rooms and devices
                    RemoveDeviceAdapter adapterDevice = new RemoveDeviceAdapter(getContext(),listDevice);
                    recyclerViewDevice.setAdapter(adapterDevice);

                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
    }
}