package esan.tablayout.Fragment;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.viewpager.widget.ViewPager;

import esan.tablayout.Adapter.AddMenuViewPager;
import esan.tablayout.Adapter.ViewPagerAdapter;
import esan.tablayout.R;
import esan.tablayout.SharedPrefManager;

public class DeviceMenu extends Fragment {

    private ViewPager viewPager;
    private AddMenuViewPager adapter;
    private SwipeRefreshLayout swipeRefreshLayout;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.device_menu, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        viewPager = getView().findViewById(R.id.viewpager);
        adapter = new AddMenuViewPager(getActivity().getSupportFragmentManager());
        viewPager.setAdapter(adapter);

        swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipeRefreshLayout);

        // SetOnRefreshListener on SwipeRefreshLayout
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {

                viewPager = getView().findViewById(R.id.viewpager);
                adapter = new AddMenuViewPager(getActivity().getSupportFragmentManager());
                viewPager.setAdapter(adapter);
                swipeRefreshLayout.setRefreshing(false);
            }
        });

    }
}
