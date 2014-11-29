package com.scopyk.fydeph.view;

//http://creandoandroid.es/implementar-navigation-drawer-menu-lateral/

import java.util.Collections;
import java.util.Collection;
import java.util.ArrayList;
import java.util.List;

import org.json.JSONException;
import org.json.JSONObject;

import com.scopyk.fydeph.APICall;
import com.scopyk.fydeph.APICallback;
import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.support.v4.widget.SwipeRefreshLayout;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListView;

public class MainActivity extends Activity implements APICallback {

	private List<MenuLabel> drawerOptions;
	private PostListAdapter postListAdapter;
    private SwipeRefreshLayout swipeRefreshLayout;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        setLoadingScreen(true);             
        
        postListAdapter = new PostListAdapter(this, android.R.id.text1, new ArrayList<Post>());
        ListView rr = (ListView)findViewById(R.id.postlistview);
        rr.setAdapter(postListAdapter);
        rr.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
				Post p = postListAdapter.getItem(arg2);
				if (p==null){
					if (!swipeRefreshLayout.isRefreshing()){						
						new APICall(MainActivity.this).execute(Content.get().getQuery(postListAdapter.getLastPostId()),"2");
						setLoading(true);
						postListAdapter.isLoading(true);
					}
					return;
				}
		        Intent intentApp = new Intent(MainActivity.this, PostViewActivity.class);
				intentApp.putExtra("postId", (String)p.getId());
		        startActivity(intentApp);
			}
		});  

		ListView drawer = (ListView) findViewById(R.id.drawer);
		drawer.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
				MenuLabel ml = drawerOptions.get(arg2);
				if (ml instanceof Folder)
					Content.get().viewFolder(ml.getId());
				else if (ml instanceof Feed)
					Content.get().viewFeed(ml.getId());
				else if (ml instanceof Tag)
					Content.get().viewTag(ml.getId());
				new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
				setLoading(true);
				((DrawerLayout)findViewById(R.id.drawer_layout)).closeDrawers();
			}
		});
		
		swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
		swipeRefreshLayout.setColorSchemeResources(R.color.green,R.color.purple,
        											R.color.yellow,R.color.orange);
		swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
            	new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
				setLoading(true);
            }
        });
		
		/*
        rr.setOnScrollListener(new OnScrollListener() {
            @Override
            public void onScrollStateChanged(AbsListView view, int scrollState) {
            }
            int lastItem;
			@Override
			public void onScroll(AbsListView view, int firstVisibleItem,
					int visibleItemCount, int totalItemCount) {
				int last = firstVisibleItem + visibleItemCount;				
				if (!swipeRefreshLayout.isRefreshing() && postListAdapter.hasLoadMore() && lastItem!=last){
					lastItem=last;
					if (last==totalItemCount){
						swipeRefreshLayout.setRefreshing(true);
						postListAdapter.removeLoadMore();
				        postListAdapter.notifyDataSetChanged();
						new ReloadTask(false).execute();
					}
				}
			}
        });
		*/
        new APICall(this).execute("arch?token="+Content.get().getToken());
    }
	
	@Override
	public void APIResponse(JSONObject json, int id) throws JSONException {
		switch(id){
			case 0: // ARCH
				Content.get().reloadStructure(json);
				updateDrawer();
				setLoadingScreen(false);
				new APICall(this).execute(Content.get().getQuery(),"1");
				setLoading(true);
				break;
			case 1: // GetPosts
				Content.get().resetPosts();
				postListAdapter.emptyList();
				Content.get().addPosts(json);
				for (Post p : Content.get().getOrderedPosts()){
					postListAdapter.add(p);
				}
				postListAdapter.addLoadMore();
		        postListAdapter.notifyDataSetChanged();
		        setLoading(false);
				break;
				
			case 2: // GetMorePosts
				int idx = Content.get().getOrderedPosts().size();
				Content.get().addPosts(json);

				List<Post> posts = Content.get().getOrderedPosts();
				for (int j=idx;j<posts.size();j++){
					postListAdapter.add(posts.get(j));
				}
				
		        postListAdapter.notifyDataSetChanged();
				setLoading(false);
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
        drawer.setAdapter(new DrawerListAdapter(this, android.R.id.text1, drawerOptions));
	}
	
	public void setLoadingScreen(boolean val){
		if (val){
	        findViewById(R.id.loadingtext).setVisibility(View.VISIBLE);
	        findViewById(R.id.posts_layout).setVisibility(View.INVISIBLE);
		} else {
			findViewById(R.id.loadingtext).setVisibility(View.INVISIBLE);
			findViewById(R.id.posts_layout).setVisibility(View.VISIBLE);
		}
	}
	
	public void setLoading(boolean val){
		swipeRefreshLayout.setRefreshing(val);
		if (!val)
			postListAdapter.isLoading(false);
	}
	
	 
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        switch(id){
	        case R.id.action_logout:
	  	      SharedPreferences settings = getSharedPreferences("FydephPrefsFile", 0);
		      SharedPreferences.Editor editor = settings.edit();
		      editor.putString("tokensaved", null);
		      editor.commit();
		      finish();
		      break;
	        case R.id.action_filter:
	        case R.id.action_settings:
	        case R.id.action_unlock:
	        default:
	        		return true;
        }

        return super.onOptionsItemSelected(item);
    }
    
    @Override
    public void onBackPressed() {
        moveTaskToBack(true);
    }
}
