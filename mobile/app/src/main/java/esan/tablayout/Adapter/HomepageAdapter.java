package esan.tablayout.Adapter;

import android.content.Context;
import android.graphics.Color;
import android.os.AsyncTask;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CompoundButton;
import android.widget.ImageView;
import android.widget.Switch;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.Arrays;
import java.util.HashMap;
import java.util.List;

import esan.tablayout.Model.Device;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class HomepageAdapter extends RecyclerView.Adapter<HomepageAdapter.MyViewHolder> {

    Context mContext;
    List<Device> mData;

    String[] icons = {"ic_bulb"};

    public HomepageAdapter(Context mContext, List<Device> mData) {
        this.mContext = mContext;
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {

        View view;
        view = LayoutInflater.from(mContext).inflate(R.layout.item_device, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;

    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, int position) {

        holder.textUID.setText(String.valueOf(SharedPrefManager.getInstance(mContext).getUser().getId()));
        String result = "ic_" + ((mData.get(position).getIcon()).substring(0, (mData.get(position).getIcon()).indexOf('.')));
        int id = mContext.getResources().getIdentifier("drawable/" + result, null, mContext.getPackageName());
        holder.img.setImageResource(id);
        holder.textName.setText(mData.get(position).getName());
        if (Arrays.asList(icons).contains(result)) {
            holder.textValue.setText(mData.get(position).getInput() + "%");
        } else {
            holder.textValue.setText(mData.get(position).getInput() + "ºC");
        }
        holder.textID.setText(String.valueOf(mData.get(position).getID()));
        String state = mData.get(position).getState();
        if (state.toUpperCase().contains("ON")) {
            holder.simpleSwitch.setChecked(true);
            holder.textName.setTextColor(Color.parseColor("#4494D9"));
            holder.textValue.setTextColor(Color.parseColor("#4494D9"));
            holder.img.setColorFilter(Color.parseColor("#4494D9"));
        } else {
            holder.simpleSwitch.setChecked(false);
            holder.textName.setTextColor(Color.parseColor("#6F6F6F"));
            holder.textValue.setTextColor(Color.parseColor("#6F6F6F"));
            holder.img.setColorFilter(Color.parseColor("#6F6F6F"));
        }

        holder.simpleSwitch.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    String state = "on";
                    switchState(state, holder.textID.getText().toString(), holder.textUID.getText().toString());
                    holder.textName.setTextColor(Color.parseColor("#4494D9"));
                    holder.textValue.setTextColor(Color.parseColor("#4494D9"));
                    holder.img.setColorFilter(Color.parseColor("#4494D9"));
                } else {
                    String state = "off";
                    switchState(state, holder.textID.getText().toString(), holder.textUID.getText().toString());
                    holder.textName.setTextColor(Color.parseColor("#6F6F6F"));
                    holder.textValue.setTextColor(Color.parseColor("#6F6F6F"));
                    holder.img.setColorFilter(Color.parseColor("#6F6F6F"));
                }
            }
        });
    }

    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private TextView textName, textValue, textID, textUID;
        private ImageView img;
        private Switch simpleSwitch;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            textUID = (TextView) itemView.findViewById(R.id.user_id);
            textName = (TextView) itemView.findViewById(R.id.device_name);
            textValue = (TextView) itemView.findViewById(R.id.device_value);
            textID = (TextView) itemView.findViewById(R.id.device_id);
            img = (ImageView) itemView.findViewById(R.id.device_ic);
            simpleSwitch = (Switch) itemView.findViewById(R.id.device_switch);


        }
    }

    private static void switchState(String state, String id, String uid) {

        final String simpleSwitchState = state;
        final String textID = id;
        final String textUserID = uid;

        //if it passes all the validations
        class SwitchState extends AsyncTask<Void, Void, String> {

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("state", simpleSwitchState);
                params.put("id", textID);
                params.put("user_id", textUserID);

                //returing the response
                return requestHandler.sendPostRequest(URLS.URL_STATE, params);
            }
        }

        //executing the async task
        SwitchState ss = new SwitchState();
        ss.execute();
    }
}
