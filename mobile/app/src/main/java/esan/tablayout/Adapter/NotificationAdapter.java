package esan.tablayout.Adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import esan.tablayout.Model.Notification;
import esan.tablayout.Model.Room;
import esan.tablayout.R;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.MyViewHolder> {

    Context mContext;
    List<Notification> mData;

    public NotificationAdapter(Context mContext, List<Notification> mData) {
        this.mContext = mContext;
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view;
        view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_notification, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, int position) {

        holder.textID.setText(String.valueOf(mData.get(position).getID()));
        String ic_name = (mData.get(position).getIcon()).substring(0, (mData.get(position).getIcon()).indexOf('.'));
        if ( ic_name.contains("device-alt")){
            ic_name = "device_alt";
        }
        String result = "ic_" + (ic_name);
        int id = mContext.getResources().getIdentifier("drawable/" + result, null, mContext.getPackageName());
        holder.img.setImageResource(id);
        holder.textTitle.setText(mData.get(position).getTitle());
        holder.textDescription.setText(mData.get(position).getDescription());
        String datetime = mData.get(position).getDate() + ", " + mData.get(position).getTime();
        holder.textDateTime.setText(datetime);
    }

    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private TextView textTitle, textDescription, textDateTime, textID;
        private ImageView img;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);
            textID = (TextView) itemView.findViewById(R.id.notification_id);
            textTitle = (TextView) itemView.findViewById(R.id.notification_title);
            textDescription = (TextView) itemView.findViewById(R.id.notification_description);
            textDateTime = (TextView) itemView.findViewById(R.id.notification_datetime);
            img = (ImageView) itemView.findViewById(R.id.notification_ic);
        }
    }
}
