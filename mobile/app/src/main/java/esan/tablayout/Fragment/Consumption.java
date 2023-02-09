package esan.tablayout.Fragment;

import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.viewpager.widget.ViewPager;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.ImageRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.tabs.TabLayout;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import esan.tablayout.Adapter.ViewPagerAdapter;
import esan.tablayout.Login;
import esan.tablayout.R;
import esan.tablayout.SharedPrefManager;

public class Consumption extends Fragment {

    private TabLayout tabLayout;
    private ViewPager viewPager;
    private ViewPagerAdapter adapter;
    private SwipeRefreshLayout swipeRefreshLayout;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.consumption, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        if (SharedPrefManager.getInstance(getContext()).getUser() != null) {

            tabLayout = getView().findViewById(R.id.tabs);
            viewPager = getView().findViewById(R.id.viewpager_id);
            adapter = new ViewPagerAdapter(getActivity().getSupportFragmentManager());

            // Add Fragment
            if (SharedPrefManager.getInstance(getContext()).getStatistics() != null) {
                createRoomStatistics();
            }

            viewPager.setAdapter(adapter);
            tabLayout.setupWithViewPager(viewPager);

            swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

            // SetOnRefreshListener on SwipeRefreshLayout
            swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
                @Override
                public void onRefresh() {
                    // Add Fragment
                    if (SharedPrefManager.getInstance(getContext()).getStatistics() != null) {
                        adapter.ClearAdapter();
                        createRoomStatistics();
                    }

                    viewPager.setAdapter(adapter);
                    tabLayout.setupWithViewPager(viewPager);
                    swipeRefreshLayout.setRefreshing(false);
                }
            });

        }
    }

    private void createRoomStatistics() {

        try {

            String json = SharedPrefManager.getInstance(getContext()).getStatistics();
            JSONObject jsonObject = new JSONObject(json);
            JSONArray jsonArray = jsonObject.getJSONArray("month");

            if (jsonArray != null) {
                //Traversing through all the object
                for (int i = 0; i < jsonArray.length(); i++) {
                    //Getting House object from json array
                    JSONObject month = jsonArray.getJSONObject(i);

                    String name = month.getString("month_name");
                    name = name.charAt(0) + name.substring(1).toLowerCase();

                    //int id = month.getInt("month_id");

                    //Populating House with the returned rooms and devices
                    adapter.AddFragment(new ConsumptionTabs(name), name);
                }

            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }
}