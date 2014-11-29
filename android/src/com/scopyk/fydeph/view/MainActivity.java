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
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.widget.SwipeRefreshLayout;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AbsListView;
import android.widget.AbsListView.OnScrollListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListView;
import android.widget.Toast;

public class MainActivity extends Activity implements APICallback {

	private String token;
	private List<MenuLabel> drawerOptions;
	private PostListAdapter postListAdapter;
    private SwipeRefreshLayout swipeRefreshLayout;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        setLoading(true);
        
        this.token = Content.get().getToken();
        
        postListAdapter = new PostListAdapter(this, android.R.id.text1, new ArrayList<Post>());
        ListView rr = (ListView)findViewById(R.id.postlistview);
        rr.setAdapter(postListAdapter);
        rr.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
				Post p = postListAdapter.getItem(arg2);
				if (p==null){
					if (!swipeRefreshLayout.isRefreshing()){
						swipeRefreshLayout.setRefreshing(true);
						postListAdapter.isLoading(true,MainActivity.this);
						new ReloadTask(false).execute();
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
				Toast.makeText(MainActivity.this, drawerOptions.get(arg2).getId(), Toast.LENGTH_SHORT).show();
				//((DrawerLayout) findViewById(R.id.drawer_layout)).closeDrawers();
			}
		});
		
		swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
		swipeRefreshLayout.setColorSchemeResources(R.color.green,R.color.purple,
        											R.color.yellow,R.color.orange);
		swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
            	new ReloadTask().execute();
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
				postListAdapter.addLoadMore();
				postListAdapter.notifyDataSetInvalidated();
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
        drawer.setAdapter(new DrawerListAdapter(this, android.R.id.text1, drawerOptions));
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
        if (id == R.id.action_settings) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }
    
    private void onRefreshComplete(List<String> result) {
    	/*
        mListAdapter.clear();
        for (String cheese : result) {
            mListAdapter.add(cheese);
        }
        mListAdapter.notifyDataSetChanged();
        */
        swipeRefreshLayout.setRefreshing(false);
    }


    private class ReloadTask extends AsyncTask<Void, Void, List<String>> {
        static final int TASK_DURATION = 3 * 1000;
        private boolean reloadUp;
        
        public ReloadTask(){
        	this(true);
        }
        public ReloadTask(boolean up){
        	this.reloadUp = up;
        }

        @Override
        protected List<String> doInBackground(Void... params) {
            try {
                Thread.sleep(TASK_DURATION);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
            return new ArrayList<String>();
        }

        @Override
        protected void onPostExecute(List<String> result) {
            super.onPostExecute(result);
            
			//postListAdapter.removeLoadMore();
	        //postListAdapter.notifyDataSetChanged();
            if (this.reloadUp)
            	System.out.println("RELOAD UP");
            else{
            	System.out.println("RELOAD DOWN");
            	postListAdapter.isLoading(false,MainActivity.this);
            }
            
            onRefreshComplete(result);
        }
    }
}
