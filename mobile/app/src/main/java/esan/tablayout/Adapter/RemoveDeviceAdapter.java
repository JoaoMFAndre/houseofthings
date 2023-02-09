package esan.tablayout.Adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import esan.tablayout.Model.Device;
import esan.tablayout.Model.NewDevice;
import esan.tablayout.R;

public class RemoveDeviceAdapter extends RecyclerView.Adapter<RemoveDeviceAdapter.MyViewHolder> {

    Context mContext;
    List<Device> mData;
    private int index = 0;
    public static String deviceID;

    public RemoveDeviceAdapter(Context mContext, List<Device> mData) {
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view;
        view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_new_device, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, @SuppressLint("RecyclerView") int position) {
        holder.textIp.setText(mData.get(position).getName());
        holder.textMac.setText(mData.get(position).getName());
        holder.textId.setText(String.valueOf(mData.get(position).getID()));
        String result = "ic_" + ((mData.get(position).getIcon()).substring(0, (mData.get(position).getIcon()).indexOf('.')));
        //int id = mContext.getResources().getIdentifier("drawable/" + result, null, mContext.getPackageName());
        //holder.img.setImageResource(id);

        holder.cardView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                index = position;
                notifyDataSetChanged();
            }
        });

        if (index == position) {
            holder.textIp.setTextColor(Color.parseColor("#FFFFFF"));
            holder.textMac.setTextColor(Color.parseColor("#FFFFFF"));
            holder.img.setColorFilter(Color.parseColor("#FFFFFF"));
            holder.cardView.setCardBackgroundColor(Color.parseColor("#4494D9"));
            deviceID = String.valueOf(mData.get(index).getID());
        } else {
            holder.textIp.setTextColor(Color.parseColor("#6F6F6F"));
            holder.textMac.setTextColor(Color.parseColor("#6F6F6F"));
            holder.img.setColorFilter(Color.parseColor("#6F6F6F"));
            holder.cardView.setCardBackgroundColor(Color.parseColor("#FFFFFF"));
        }
    }

    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private TextView textIp, textMac, textId;
        private ImageView img;
        private CardView cardView;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            cardView = (CardView) itemView.findViewById(R.id.device_card);
            textIp = (TextView) itemView.findViewById(R.id.device_ip);
            textId = (TextView) itemView.findViewById(R.id.device_id);
            textMac = (TextView) itemView.findViewById(R.id.device_mac);
            img = (ImageView) itemView.findViewById(R.id.device_ic);

        }
    }
}
