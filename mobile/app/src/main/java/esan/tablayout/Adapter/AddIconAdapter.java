package esan.tablayout.Adapter;

import android.annotation.SuppressLint;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import esan.tablayout.R;

public class AddIconAdapter extends RecyclerView.Adapter<AddIconAdapter.MyViewHolder> {

    List<Integer> mData;
    private int index = 0;
    public static String iconName;

    public AddIconAdapter(List<Integer> mData) {
        this.mData = mData;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view;
        view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_icon, parent, false);
        MyViewHolder viewHolder = new MyViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, @SuppressLint("RecyclerView") int position) {

        holder.img.setImageResource(mData.get(position));

        holder.cardView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                index = position;
                notifyDataSetChanged();
            }
        });

        if (index == position) {
            holder.img.setColorFilter(Color.parseColor("#FFFFFF"));
            holder.cardView.setCardBackgroundColor(Color.parseColor("#4494D9"));
            iconName = holder.img.getResources().getResourceEntryName(mData.get(index));
        } else {
            holder.img.setColorFilter(Color.parseColor("#6F6F6F"));
            holder.cardView.setCardBackgroundColor(Color.parseColor("#FFFFFF"));
        }
    }

    @Override
    public int getItemCount() {
        return mData.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {

        private ImageView img;
        private CardView cardView;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            cardView = (CardView) itemView.findViewById(R.id.ic_card);
            img = (ImageView) itemView.findViewById(R.id.ic);

        }
    }
}
