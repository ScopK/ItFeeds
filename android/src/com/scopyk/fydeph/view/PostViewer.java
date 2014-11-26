package com.scopyk.fydeph.view;

//http://creandoandroid.es/implementar-navigation-drawer-menu-lateral/

import java.util.Collections;
import java.util.Collection;
import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.scopyk.fydeph.APICall;
import com.scopyk.fydeph.APICallback;
import com.scopyk.fydeph.R;
import com.scopyk.fydeph.R.id;
import com.scopyk.fydeph.R.layout;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class PostViewer extends Activity implements APICallback {

	private String token;
	private List<MenuLabel> drawerOptions;
	private PostListAdapter postListAdapter;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.post_viewer);
        setLoading(true);
        
        postListAdapter = new PostListAdapter(this, android.R.id.text1, new ArrayList<Post>());
        ListView rr = (ListView)findViewById(R.id.postlistview);
        rr.setAdapter(postListAdapter);
        rr.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {			
		        Intent intentApp = new Intent(PostViewer.this, ViewerActivity.class);
				intentApp.putExtra("postId", (String)postListAdapter.getItem(arg2).getId());
		        startActivity(intentApp);
			}
		});

		ListView drawer = (ListView) findViewById(R.id.drawer);
		drawer.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
				Toast.makeText(PostViewer.this, drawerOptions.get(arg2).getId(), Toast.LENGTH_SHORT).show();
				//((DrawerLayout) findViewById(R.id.drawer_layout)).closeDrawers();
			}
		});
		
        this.token = Content.get().getToken();
        new APICall(this).execute("arch?token="+token);
    }
	
	@Override
	public void APIResponse(JSONObject json, int id) throws JSONException {
		switch(id){
			case 0: // ARCH
				Content.get().reloadStructure(json);
				updateDrawer();
				setLoading(false);
				new APICall(this).execute("posts?token="+token,"1");
				break;
			case 1: // GetPosts

				Content.get().resetPosts();
				Content.get().addPosts(json);
				//ListView rr = (ListView)findViewById(R.id.postlistview);
				int i=0;
				for (Post p : Content.get().getOrderedPosts()){
					postListAdapter.add(p);
					/*
    				TextView tv1 = new TextView(this);
                    tv1.setId((int)System.currentTimeMillis()+i);
                    //lp = new RelativeLayout.LayoutParams(LayoutParams.WRAP_CONTENT,LayoutParams.WRAP_CONTENT);
                    //lp.addRule(RelativeLayout.BELOW, recent.getId());
                    tv1.setText(p.getTitle());

                    rr.addView(tv1,i);
                    i++;*/
				}
		        postListAdapter.notifyDataSetChanged();
				break;
		}
			
	}
	
	public void updateDrawer(){
        ListView drawer = (ListView) findViewById(R.id.drawer);
        //DrawerLayout drawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
		
        drawerOptions = new ArrayList<MenuLabel>();
        Collection<Folder> values = Content.get().getFolders().values();
        
        Folder fnull = null;
        for (Folder f : values){
        	if (f.getName().equals("null"))
        		fnull = f;
        	else
        		drawerOptions.add(f);
        }
		Collections.sort(drawerOptions, new LabelComparator());
        if (fnull != null){
        	Collections.sort(fnull.getFeeds(), new LabelComparator());
	        for (Feed f : fnull.getFeeds()){
	        	drawerOptions.add(f);
	        }
        }
        drawer.setAdapter(new ItemListAdapter(this, android.R.id.text1, drawerOptions));
	}
	
	public void setLoading(boolean val){
		if (val){
	        findViewById(R.id.loadingtext).setVisibility(View.VISIBLE);
	        findViewById(R.id.posts_layout).setVisibility(View.INVISIBLE);
		} else {
			findViewById(R.id.loadingtext).setVisibility(View.INVISIBLE);
			findViewById(R.id.posts_layout).setVisibility(View.VISIBLE);
		}
	}

}
