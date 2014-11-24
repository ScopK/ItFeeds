package com.scopyk.fydeph;

import java.util.List;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class ItemListAdapter extends BaseAdapter {

	 private Activity activity;
	 private List<String> list;
	 
	 
	 public ItemListAdapter(Activity activity, List<String> list){
		 this.activity = activity;
		 this.list = list;  
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
		//ListItem lstItem = new ListItem(activity, getItem(position));
		//return lstItem;
	    TextView text = (TextView) convertView.findViewById(R.id.textView1);
	    text.setText("Position " + position);

        return convertView;
	 }


	}
