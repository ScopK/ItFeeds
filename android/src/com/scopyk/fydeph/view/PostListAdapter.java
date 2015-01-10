package com.scopyk.fydeph.view;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.List;

import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.Post;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.FrameLayout;
import android.widget.TextView;

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
		 if (this.hasLoadMore)
			 this.list.add(this.list.size()-1,p);
		 else
			 this.list.add(p);
		 
	 }
	 public void emptyList(){
		 this.list.clear();
		 this.hasLoadMore = false;
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
			 this.nullView = null;
		 }
	 }
	 
	 public boolean hasLoadMore(){
		 return this.hasLoadMore;
	 }
	 
	 public void isLoading(boolean loading){
		 if (this.hasLoadMore && nullView != null){
			 TextView text;
			 text = (TextView) nullView.findViewById(R.id.textView1);
			 if (loading)
				 text.setText(R.string.loading);
			 else
				 text.setText(R.string.load_more);
		 }
	 }
	 
	 public int getPostsCount() {
		 if (this.hasLoadMore)
			 return list.size()-1;
		 else
			 return list.size();
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
	 
	 public String getLastPostId() {
		 int i=(this.hasLoadMore)?2:1;
		 return this.list.get(this.list.size()-i).getId();
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
        TextView title,date;
        FrameLayout bar;
		//if (convertView != null) 
		//	view = convertView;
		//else
			view = mInflater.inflate(R.layout.post_line_item, parent, false);
			
		try{
	    	title = (TextView) view.findViewById(R.id.post_title);
	    	title.setText(item.getTitle());
	    	if (item.getUnread())
	    		title.setTypeface(null, android.graphics.Typeface.BOLD);
	    	
	        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
	        date = (TextView) view.findViewById(R.id.post_date);
	    	date.setText(df.format(item.getDate()));
	    	
	    	if (item.getFavorite()){
		    	bar = (FrameLayout) view.findViewById(R.id.statusBar);
		    	//bar.setBackgroundColor(Color.parseColor("#ff0000"));
		    	bar.setBackgroundColor(Color.RED);
	    	}
		}catch(Exception e){}

        return view;
	 }

}
