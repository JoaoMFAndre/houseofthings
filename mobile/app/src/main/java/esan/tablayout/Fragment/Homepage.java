package esan.tablayout.Fragment;

import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.viewpager.widget.ViewPager;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.ImageRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.tabs.TabLayout;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Calendar;
import java.util.Date;

import de.hdodenhof.circleimageview.CircleImageView;
import esan.tablayout.Adapter.ViewPagerAdapter;
import esan.tablayout.Login;
import esan.tablayout.R;
import esan.tablayout.SharedPrefManager;

public class Homepage extends Fragment {

    Button logout_btn;
    TextView name, greeting;
    CircleImageView avatar;

    private TabLayout tabLayout;
    private ViewPager viewPager;
    private ViewPagerAdapter adapter;
    private SwipeRefreshLayout swipeRefreshLayout;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.homepage, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        if (SharedPrefManager.getInstance(getContext()).getUser() != null) {

            greeting = getView().findViewById(R.id.textGreeting);

            //Get the time of day
            Date date = new Date();
            Calendar cal = Calendar.getInstance();
            cal.setTime(date);
            int hour = cal.get(Calendar.HOUR_OF_DAY);

            //Set greeting
            if(hour>=6 && hour<12){
                greeting.setText(getResources().getString(R.string.greetings1_title));
            } else if(hour>= 12 && hour < 17){
                greeting.setText(getResources().getString(R.string.greetings2_title));
            } else if(hour >= 17 && hour < 21){
                greeting.setText(getResources().getString(R.string.greetings3_title));
            } else if(hour >= 21 && hour < 24){
                greeting.setText(getResources().getString(R.string.greetings3_title));
            }

            name = getView().findViewById(R.id.textName);
            String getName = SharedPrefManager.getInstance(getContext()).getUser().getName();
            String capitalizedName = (getName.substring(0, 1).toUpperCase() + getName.substring(1)).trim();
            name.setText(capitalizedName);

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

            tabLayout = getView().findViewById(R.id.tabs);
            viewPager = getView().findViewById(R.id.viewpager_id);
            adapter = new ViewPagerAdapter(getActivity().getSupportFragmentManager());

            // Add Fragment
            if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {
                createRoom();
            }

            viewPager.setAdapter(adapter);
            tabLayout.setupWithViewPager(viewPager);

            swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

            // SetOnRefreshListener on SwipeRefreshLayout
            swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
                @Override
                public void onRefresh() {

                    // Add Fragment
                    if (SharedPrefManager.getInstance(getContext()).getHouse() != null) {
                        adapter.ClearAdapter();
                        createRoom();
                    }

                    viewPager.setAdapter(adapter);
                    tabLayout.setupWithViewPager(viewPager);
                    swipeRefreshLayout.setRefreshing(false);

                }
            });

        }
    }

    private void createRoom() {

        try {

            String json = SharedPrefManager.getInstance(getContext()).getHouse();
            JSONObject jsonObject = new JSONObject(json);
            JSONArray jsonArray = jsonObject.getJSONArray("room");

            if (jsonArray != null) {
                //Traversing through all the object
                for (int i = 0; i < jsonArray.length(); i++) {
                    //Getting House object from json array
                    JSONObject house = jsonArray.getJSONObject(i);

                    String name = house.getString("name");
                    name = name.charAt(0) + name.substring(1).toLowerCase();

                    int id = house.getInt("id");

                    //Populating House with the returned rooms and devices
                    adapter.AddFragment(new HomepageTabs(id), name);
                }

            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }
}