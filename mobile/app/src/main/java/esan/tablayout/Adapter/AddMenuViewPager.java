package esan.tablayout.Adapter;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;

import esan.tablayout.Fragment.Add_Device;
import esan.tablayout.Fragment.Edit_Device;
import esan.tablayout.Fragment.Remove_Device;

public class AddMenuViewPager extends FragmentPagerAdapter {

    public AddMenuViewPager(FragmentManager fm) {
        super(fm);
    }

    @Override
    public Fragment getItem(int position) {
        if (position == 0) {
            return new Add_Device();
        } else if (position == 1) {
            return new Edit_Device();
        } else {
            return new Remove_Device();
        }
    }

    @Override
    public int getCount() {
        return 3;
    }
}

