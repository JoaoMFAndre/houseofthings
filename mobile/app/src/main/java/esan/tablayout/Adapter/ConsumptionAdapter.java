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
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import java.util.Arrays;
import java.util.HashMap;
import java.util.List;

import esan.tablayout.Model.Device;
import esan.tablayout.Model.Month;
import esan.tablayout.Model.Room;
import esan.tablayout.Model.Statistics;
import esan.tablayout.R;
import esan.tablayout.RequestHandler;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class ConsumptionAdapter extends RecyclerView.Adapter<ConsumptionAdapter.MyViewHolder> {

    Context mContext;
    List<Statistics> mData;

    public ConsumptionAdapter(Context mContext, List<Statistics> mData) {
        this.mContext = mContext;
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {

        View view;
        view = LayoutInflater.from(mContext).inflate(R.layout.item_room_consumption, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;

    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, int position) {

        holder.textName.setText(mData.get(position).getrName());
        holder.textNumber.setText(mData.get(position).getrName());
        holder.textConsumption.setText(String.valueOf(mData.get(position).getConsumption()) + " kWh");
    }

    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private TextView textName, textNumber, textConsumption;
        private ImageView img;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            textConsumption = (TextView) itemView.findViewById(R.id.room_consumption);
            textName = (TextView) itemView.findViewById(R.id.room_name);
            textNumber = (TextView) itemView.findViewById(R.id.room_devices);
            img = (ImageView) itemView.findViewById(R.id.room_ic);

        }
    }
}
