package esan.tablayout.Adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import esan.tablayout.R;
import esan.tablayout.Model.Room;

public class AddRoomAdapter extends RecyclerView.Adapter<AddRoomAdapter.MyViewHolder> {

    List<Room> mData;
    private int index = 0;
    public static String roomID;

    public AddRoomAdapter(List<Room> mData) {
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view;
        view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_room, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, @SuppressLint("RecyclerView") int position) {

        holder.textName.setText(mData.get(position).getName());
        holder.textNumber.setText(mData.get(position).getName());
        holder.textId.setText(String.valueOf(mData.get(position).getID()));

        if (mData.get(position).getID() == 0) {
            holder.textName.setVisibility(View.GONE);
            holder.textNumber.setVisibility(View.GONE);
            holder.img.setImageResource(R.drawable.ic_add);
            holder.img.setColorFilter(Color.parseColor("#4494D9"));
        }

        holder.cardView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                index = position;
                notifyDataSetChanged();
                if (mData.get(index).getID() == 0) {
                    ((Activity) view.getContext()).findViewById(R.id.text_room_name).setVisibility(View.VISIBLE);
                } else {
                    ((Activity) view.getContext()).findViewById(R.id.text_room_name).setVisibility(View.GONE);
                }
            }
        });

        if (index == position) {
            if (mData.get(index).getID() == 0) {
                holder.img.setColorFilter(Color.parseColor("#FFFFFF"));
                holder.cardView.setCardBackgroundColor(Color.parseColor("#4494D9"));
            } else {
                holder.textName.setTextColor(Color.parseColor("#FFFFFF"));
                holder.textNumber.setTextColor(Color.parseColor("#FFFFFF"));
                holder.img.setColorFilter(Color.parseColor("#FFFFFF"));
                holder.cardView.setCardBackgroundColor(Color.parseColor("#4494D9"));
                roomID = String.valueOf(mData.get(index).getID());
            }
        } else {
            holder.textName.setTextColor(Color.parseColor("#6F6F6F"));
            holder.textNumber.setTextColor(Color.parseColor("#6F6F6F"));
            holder.img.setColorFilter(Color.parseColor("#6F6F6F"));
            holder.cardView.setCardBackgroundColor(Color.parseColor("#FFFFFF"));
        }
    }


    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private TextView textName, textNumber, textId;
        private ImageView img;
        private CardView cardView;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            cardView = (CardView) itemView.findViewById(R.id.room_card);
            textName = (TextView) itemView.findViewById(R.id.room_name);
            textId = (TextView) itemView.findViewById(R.id.room_id);
            textNumber = (TextView) itemView.findViewById(R.id.room_devices);
            img = (ImageView) itemView.findViewById(R.id.room_ic);

        }
    }
}
