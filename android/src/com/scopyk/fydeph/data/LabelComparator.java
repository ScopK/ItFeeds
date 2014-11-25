package com.scopyk.fydeph.data;

import java.util.Comparator;

public class LabelComparator implements Comparator<MenuLabel> {
    @Override
    public int compare(MenuLabel o1, MenuLabel o2) {
        return o1.getLabel().compareTo(o2.getLabel());
    }
}