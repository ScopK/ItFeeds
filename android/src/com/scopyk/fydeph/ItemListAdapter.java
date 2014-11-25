package com.scopyk.fydeph;

import java.util.List;

import com.scopyk.fydeph.data.Feed;
import com.scopyk.fydeph.data.MenuLabel;

import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class ItemListAdapter extends BaseAdapter {

	 private Context activity;
	 private List<MenuLabel> list;
	 private int textViewResourceId;
	 private LayoutInflater mInflater;
	 
	 public ItemListAdapter(Context activity, int textViewResourceId,List<MenuLabel> list){
		 this.activity = activity;
		 this.list = list;
		 this.textViewResourceId = textViewResourceId;
		 this.mInflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	 }
	 
	 public int getCount() {
		 return list.size();
	 }

	 public Object getItem(int position) {
	  return list.get(position);
	 }

	 public long getItemId(int position) {  
		 return position;
	 }

	@Override
	 public View getView(int position, View convertView, ViewGroup parent) {
        View view;
        TextView text;
        
        MenuLabel item = list.get(position);

        if (convertView == null) {
            if (item instanceof Feed)
            	view = mInflater.inflate(R.layout.menuitem_normal, parent, false);
            else
            	view = mInflater.inflate(R.layout.menuitem_bold, parent, false);
        } else view = convertView;

        try {
	        text = (TextView) view;
	        text.setText(item.getLabel());
	        
        } catch (ClassCastException e) {
            Log.e("ArrayAdapter", "You must supply a resource ID for a TextView");
            throw new IllegalStateException(
                    "ArrayAdapter requires the resource ID to be a TextView", e);
        }

        return view;
	 }
	}
