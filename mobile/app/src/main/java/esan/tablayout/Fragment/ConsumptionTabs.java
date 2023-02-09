package esan.tablayout.Fragment;

import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.content.res.ResourcesCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.github.mikephil.charting.animation.Easing;
import com.github.mikephil.charting.charts.PieChart;
import com.github.mikephil.charting.components.Legend;
import com.github.mikephil.charting.data.PieData;
import com.github.mikephil.charting.data.PieDataSet;
import com.github.mikephil.charting.data.PieEntry;
import com.github.mikephil.charting.formatter.PercentFormatter;
import com.github.mikephil.charting.utils.ColorTemplate;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import esan.tablayout.Adapter.AddRoomAdapter;
import esan.tablayout.Adapter.ConsumptionAdapter;
import esan.tablayout.Adapter.HomepageAdapter;
import esan.tablayout.Interface.VolleyCallBack;
import esan.tablayout.Model.Device;
import esan.tablayout.Model.Month;
import esan.tablayout.Model.Room;
import esan.tablayout.Model.Statistics;
import esan.tablayout.R;
import esan.tablayout.SharedPrefManager;
import esan.tablayout.URLS;

public class ConsumptionTabs extends Fragment {

    View view;
    private RecyclerView myrecyclerview;
    private ArrayList<Statistics> listStatistics;
    private ArrayList<PieEntry> entries;
    private String month_name;
    private PieChart pieChart;

    public ConsumptionTabs(String string) {
        this.month_name = string;
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.consumption_tab, container, false);
        myrecyclerview = (RecyclerView) view.findViewById(R.id.recycler);
        ConsumptionAdapter recyclerViewAdapter = new ConsumptionAdapter(getContext(), listStatistics);

        LinearLayoutManager linearLayoutManager = new LinearLayoutManager(getContext());
        linearLayoutManager.setOrientation(linearLayoutManager.HORIZONTAL);

        myrecyclerview.setLayoutManager(linearLayoutManager);
        myrecyclerview.setAdapter(recyclerViewAdapter);

        pieChart = view.findViewById(R.id.consumption_piechart);
        setupPieChart();

        return view;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (listStatistics == null) {
            listStatistics = new ArrayList<>();
            entries = new ArrayList<>();
        } else {
            listStatistics.clear();
            listStatistics = new ArrayList<>();
            entries.clear();
            entries = new ArrayList<>();
        }

        createRoomStatistics();

    }

    private void setupPieChart() {

        ArrayList<Integer> colors = new ArrayList<>();
        for (int color: ColorTemplate.MATERIAL_COLORS) {
            colors.add(color);
        }

        for (int color: ColorTemplate.VORDIPLOM_COLORS) {
            colors.add(color);
        }

        PieDataSet dataSet = new PieDataSet(entries, "");
        dataSet.setColors(colors);
        dataSet.setSliceSpace(5);

        PieData data = new PieData(dataSet);
        data.setDrawValues(true);
        data.setValueFormatter(new PercentFormatter(pieChart));
        data.setValueTextSize(12f);
        data.setValueTextColor(Color.BLACK);
        data.setValueTypeface(ResourcesCompat.getFont(getContext(), R.font.chivo_regular));

        pieChart.setData(data);
        pieChart.invalidate();

        pieChart.setDrawHoleEnabled(true);
        pieChart.setUsePercentValues(true);
        pieChart.setEntryLabelTextSize(12);
        pieChart.setEntryLabelTypeface(ResourcesCompat.getFont(getContext(), R.font.chivo_regular));
        pieChart.setEntryLabelColor(Color.BLACK);
        pieChart.setCenterText("Total in Month");
        pieChart.setCenterTextSize(24);
        pieChart.setCenterTextTypeface(ResourcesCompat.getFont(getContext(), R.font.alfa_slab_one));
        pieChart.setCenterTextColor(Color.parseColor("#4494D9"));
        pieChart.getDescription().setEnabled(false);
        pieChart.getLegend().setEnabled(false);
    }

    private void createRoomStatistics() {

        if (SharedPrefManager.getInstance(getContext()).getStatistics() != null) {

            try {

                String json = SharedPrefManager.getInstance(getContext()).getStatistics();
                JSONObject jsonObject = new JSONObject(json);
                JSONArray jsonArray = jsonObject.getJSONArray("statistics");

                if (jsonArray != null) {

                    //Traversing through all the object
                    for (int i = 0; i < jsonArray.length(); i++) {
                        //Getting House object from json array
                        JSONObject statistics = jsonArray.getJSONObject(i);

                        if (statistics.getString("month").equals(month_name)) {
                            listStatistics.add(new Statistics(
                                    statistics.getInt("id"),
                                    statistics.getInt("day"),
                                    statistics.getString("month"),
                                    statistics.getInt("year"),
                                    statistics.getInt("consumption"),
                                    statistics.getInt("room_id"),
                                    statistics.getInt("total"),
                                    statistics.getString("room_name")
                            ));
                            entries.add(new PieEntry(
                                    statistics.getInt("consumption"),
                                    statistics.getString("room_name")
                            ));
                        }
                    }
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
    }

}
