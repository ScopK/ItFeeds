package com.scopyk.fydeph.view;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.List;

import com.scopyk.fydeph.R;
import com.scopyk.fydeph.R.layout;
import com.scopyk.fydeph.data.Post;
import com.scopyk.fydeph.data.MenuLabel;

import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class PostListAdapter extends BaseAdapter {

	 private Context activity;
	 private List<Post> list;
	 private int textViewResourceId;
	 private LayoutInflater mInflater;
	 
	 public PostListAdapter(Context activity, int textViewResourceId,List<Post> list){
		 this.activity = activity;
		 this.list = list;
		 this.textViewResourceId = textViewResourceId;
		 this.mInflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	 }
	 
	 public void add(Post p){
		 this.list.add(p);
	 }
	 
	 public int getCount() {
		 return list.size();
	 }

	 public Post getItem(int position) {
		 return list.get(position);
	 }

	 @Override
	 public long getItemId(int position) {  
		 return position;
	 }

	 @Override
	 public View getView(int position, View convertView, ViewGroup parent) {
        View view;
        TextView title,date;
        
        Post item = list.get(position);

        if (convertView == null) {
            view = mInflater.inflate(R.layout.post_line_item, parent, false);
        } else view = convertView;

        try {
        	title = (TextView) view.findViewById(R.id.post_title);
        	title.setText(item.getTitle());
        	if (item.getUnread())
        		title.setTypeface(null, android.graphics.Typeface.BOLD);
            DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            date = (TextView) view.findViewById(R.id.post_date);
        	date.setText(df.format(item.getDate()));
	        
        } catch (ClassCastException e) {
            Log.e("ArrayAdapter", "You must supply a resource ID for a TextView");
            throw new IllegalStateException(
                    "ArrayAdapter requires the resource ID to be a TextView", e);
        }

        return view;
	 }
	}
