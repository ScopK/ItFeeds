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
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
import android.widget.Toast;

public class PostListAdapter extends BaseAdapter {

	 private List<Post> list;
	 private LayoutInflater mInflater;
	 private boolean hasLoadMore;
	 private View nullView;
	 
	 public PostListAdapter(Context activity, int textViewResourceId,List<Post> list){
		 this.list = list;
		 this.mInflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		 this.hasLoadMore = false;
	 }
	 
	 public void add(Post p){
		 this.list.add(p);
	 }
	 
	 public void addLoadMore(){
		 if (!this.hasLoadMore){
			 this.list.add(null);
			 this.hasLoadMore = true;
		 }
	 }
	 
	 public void removeLoadMore(){
		 if (this.hasLoadMore){
			 this.list.remove(null);
			 this.hasLoadMore = false;
		 }
	 }
	 
	 public boolean hasLoadMore(){
		 return this.hasLoadMore;
	 }
	 
	 public void isLoading(boolean loading,Activity a){
		 if (this.hasLoadMore){
			 TextView text;
			 if (nullView==null){
				 Toast.makeText(a, ""+list.size(), Toast.LENGTH_SHORT).show();
				 return;
			 }
			 text = (TextView) nullView.findViewById(R.id.textView1);
			 if (loading)
				 text.setText(R.string.loading);
			 else
				 text.setText(R.string.load_more);
		 }
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
        Post item = list.get(position);
        if (item == null){
        	if (nullView==null)
        		nullView = mInflater.inflate(R.layout.loadmore_line_item, parent, false);
    		return nullView;
        }
        View view;
		if (convertView != null) 
			view = convertView;
		else
			view = mInflater.inflate(R.layout.post_line_item, parent, false);
		

        TextView title,date;
    	
    	title = (TextView) view.findViewById(R.id.post_title);
    	title.setText(item.getTitle());
    	if (item.getUnread())
    		title.setTypeface(null, android.graphics.Typeface.BOLD);
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        date = (TextView) view.findViewById(R.id.post_date);
    	date.setText(df.format(item.getDate()));

        return view;
	 }

}
