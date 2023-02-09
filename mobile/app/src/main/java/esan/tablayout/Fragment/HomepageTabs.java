package esan.tablayout.Fragment;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Adapter.HomepageAdapter;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.Model.Device;
import esan.tablayout.R;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class HomepageTabs extends Fragment {

    View view;
    private RecyclerView myrecyclerview;
    private ArrayList<Device> listDevice;
    private int room_id;

    public HomepageTabs(int id) {
        this.room_id = id;
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.homepage_tab, container, false);
        myrecyclerview = (RecyclerView) view.findViewById(R.id.recycler);
        HomepageAdapter recyclerViewAdapter = new HomepageAdapter(getContext(), listDevice);
        GridLayoutManager gridLayoutManager = new GridLayoutManager(getContext(), 2, GridLayoutManager.VERTICAL, false);
        myrecyclerview.setLayoutManager(gridLayoutManager);
        myrecyclerview.setAdapter(recyclerViewAdapter);

        return view;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (listDevice == null) {
            listDevice = new ArrayList<>();
        } else {
            listDevice.clear();
            listDevice = new ArrayList<>();
        }
        createDevice();

    }

    private void createDevice() {

        try {

            String json = SharedPrefManager.getInstance(getContext()).getHouse();
            JSONObject jsonObject = new JSONObject(json);
            JSONArray jsonArray = jsonObject.getJSONArray("device");

            if (jsonArray != null) {
                //Traversing through all the object
                for (int i = 0; i < jsonArray.length(); i++) {
                    //Getting House object from json array
                    JSONObject house = jsonArray.getJSONObject(i);

                    if (house.getInt("room_id") == room_id) {
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
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

}
