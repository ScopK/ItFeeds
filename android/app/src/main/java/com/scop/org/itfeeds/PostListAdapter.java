package com.scop.org.itfeeds;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;


import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.FrameLayout;
import android.widget.TextView;

import com.scop.org.itfeeds.data.Post;

/**
 * Created by Oscar on 22/11/2015.
 */
public class PostListAdapter extends BaseAdapter {
    private List<Post> list;
    private LayoutInflater mInflater;

    public PostListAdapter(Context activity){
        this.list = new ArrayList<Post>();
        this.mInflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    public void add(Post p){
        this.list.add(p);
    }
    public void emptyList(){
        this.list.clear();
    }

    public void isLoading(boolean loading){
        /*if (this.hasLoadMore && nullView != null){
            TextView text;
            text = (TextView) nullView.findViewById(R.id.textView1);
            if (loading)
                text.setText(R.string.loading);
            else
                text.setText(R.string.load_more);
        }*/
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
        return this.list.get(this.list.size()-1).getId();
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        Post item = list.get(position);

        View view;
        TextView title,date;
        FrameLayout bar;
        //view = mInflater.inflate(android.R.layout.simple_list_item_2, parent, false);
        view = mInflater.inflate(R.layout.post_list_item, parent, false);

        try{
            //title = (TextView) view.findViewById(android.R.id.text1);
            title = (TextView) view.findViewById(R.id.post_title);
            title.setText(item.getTitle());
            if (item.getUnread())
                title.setTypeface(null, android.graphics.Typeface.BOLD);

            DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            //date = (TextView) view.findViewById(android.R.id.text2);
            date =  (TextView) view.findViewById(R.id.post_date);
            date.setText("["+item.getFeed().getName()+"] "+df.format(item.getDate()));

            if (item.getFavorite()){
                bar = (FrameLayout) view.findViewById(R.id.statusBar);
                bar.setBackgroundResource(R.color.colorPrimaryDark);
            }
        }catch(Exception e){}

        return view;
    }

}
