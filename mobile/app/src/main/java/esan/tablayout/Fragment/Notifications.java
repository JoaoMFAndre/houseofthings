package esan.tablayout.Fragment;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ObjectAnimator;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.TranslateAnimation;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.ItemTouchHelper;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.snackbar.Snackbar;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Adapter.AddRoomAdapter;
import esan.tablayout.Adapter.HomepageAdapter;
import esan.tablayout.Adapter.NotificationAdapter;
import esan.tablayout.Dashboard;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.Model.Notification;
import esan.tablayout.Model.Room;
import esan.tablayout.Model.User;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class Notifications extends Fragment {

    private ArrayList<Notification> listNotification;
    RecyclerView recyclerView;
    ConstraintLayout constraintLayout;
    LinearLayout linearLayout, info;
    NotificationAdapter adapter;
    SwipeRefreshLayout swipeRefreshLayout;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        return inflater.inflate(R.layout.notifications, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        constraintLayout = view.findViewById(R.id.f1);

        linearLayout = view.findViewById(R.id.clear_all);

        listNotification = new ArrayList<>();

        info = view.findViewById(R.id.noNotifications);

        recyclerView = view.findViewById(R.id.recyclerAdd);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));

        //Create Notification
        createNotification();
        ItemTouchHelper helper = new ItemTouchHelper(simpleCallback);
        helper.attachToRecyclerView(recyclerView);

        linearLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Animation animation = new TranslateAnimation(0, -5000, 0, 0);
                animation.setDuration(800);
                animation.setAnimationListener(new Animation.AnimationListener() {
                    @Override
                    public void onAnimationStart(Animation animation) {

                    }

                    @Override
                    public void onAnimationEnd(Animation animation) {
                        Toast.makeText(getContext(), getResources().getString(R.string.notifications_message), Toast.LENGTH_SHORT).show();

                        deleteAllNotifications();

                        listNotification.clear();
                        adapter.notifyDataSetChanged();

                        SharedPrefManager.getInstance(getContext()).deleteKey("notification");
                    }

                    @Override
                    public void onAnimationRepeat(Animation animation) {

                    }
                });
                recyclerView.startAnimation(animation);
            }
        });

        swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

        // SetOnRefreshListener on SwipeRefreshLayout
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                reloadNotification(new VolleyCallBack() {
                    @Override
                    public void onSuccess() {
                        listNotification.clear();
                        listNotification = new ArrayList<>();
                        createNotification();
                        ItemTouchHelper helper = new ItemTouchHelper(simpleCallback);
                        helper.attachToRecyclerView(recyclerView);
                        swipeRefreshLayout.setRefreshing(false);
                    }
                });
            }
        });
    }

    ItemTouchHelper.SimpleCallback simpleCallback = new ItemTouchHelper.SimpleCallback(0, ItemTouchHelper.LEFT | ItemTouchHelper.RIGHT) {
        @Override
        public boolean onMove(@NonNull RecyclerView recyclerView, @NonNull RecyclerView.ViewHolder viewHolder, @NonNull RecyclerView.ViewHolder target) {
            return false;
        }

        @Override
        public void onSwiped(@NonNull RecyclerView.ViewHolder viewHolder, int direction) {
            Toast.makeText(getContext(), getResources().getString(R.string.notification_message), Toast.LENGTH_SHORT).show();

            String textID = String.valueOf(listNotification.get(viewHolder.getAdapterPosition()).getID());
            deleteNotification(textID);

            listNotification.remove(viewHolder.getAdapterPosition());
            adapter.notifyDataSetChanged();
        }
    };

    private void createNotification() {
        if (SharedPrefManager.getInstance(getContext()).getNotification() != null) {

            try {

                String json = SharedPrefManager.getInstance(getContext()).getNotification();
                JSONObject jsonObject = new JSONObject(json);
                JSONArray jsonArray = jsonObject.getJSONArray("notification");

                if (jsonArray != null && jsonArray.length() > 0) {

                    info.setVisibility(getView().GONE);
                    linearLayout.setVisibility(getView().VISIBLE);
                    recyclerView.setVisibility(getView().VISIBLE);

                    //Traversing through all the object
                    for (int i = 0; i < jsonArray.length(); i++) {
                        //Getting notification object from json array
                        JSONObject notification = jsonArray.getJSONObject(i);

                        listNotification.add(new Notification(
                                notification.getInt("notification_id"),
                                notification.getString("notification_icon"),
                                notification.getString("notification_time"),
                                notification.getString("notification_date"),
                                notification.getString("notification_title"),
                                notification.getString("notification_description")
                        ));
                    }
                    //Populating notification with the returned values
                    adapter = new NotificationAdapter(getContext(), listNotification);
                    recyclerView.setAdapter(adapter);
                } else {
                    recyclerView.setVisibility(getView().GONE);
                    linearLayout.setVisibility(getView().GONE);
                    info.setVisibility(getView().VISIBLE);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        } else {

            recyclerView.setVisibility(getView().GONE);
            linearLayout.setVisibility(getView().GONE);
            info.setVisibility(getView().VISIBLE);

        }
    }

    private void deleteNotification(String id) {

        //User ID
        final String userID = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());
        //Notification ID
        final String textID = id;

        //if it passes all the validations
        class DeleteNotification extends AsyncTask<Void, Void, String> {

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("user_id", userID);
                params.put("id", textID);

                //returing the response
                return requestHandler.sendPostRequest(URLS.URL_DELETE_NOTIFICATION, params);
            }
        }

        //executing the async task
        DeleteNotification dn = new DeleteNotification();
        dn.execute();
    }

    private void deleteAllNotifications() {

        //User ID
        final String userID = String.valueOf(SharedPrefManager.getInstance(getContext()).getUser().getId());

        //if it passes all the validations
        class DeleteAllNotifications extends AsyncTask<Void, Void, String> {

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("user_id", userID);

                //returing the response
                return requestHandler.sendPostRequest(URLS.URL_DELETE_ALL_NOTIFICATIONS, params);
            }
        }

        //executing the async task
        DeleteAllNotifications dan = new DeleteAllNotifications();
        dan.execute();
    }

    private void reloadNotification(final VolleyCallBack callBack) {

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

        queue.add(stringRequest);
    }
}