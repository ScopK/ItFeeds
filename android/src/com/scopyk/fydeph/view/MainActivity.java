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

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.FragmentActivity;
import android.support.v4.widget.DrawerLayout;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.ActionBar;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.internal.view.menu.MenuBuilder;
import android.support.v7.widget.ActionMenuPresenter;
import android.support.v7.widget.Toolbar;
import android.support.v7.widget.Toolbar.OnMenuItemClickListener;
import android.text.InputType;
import android.view.ContextThemeWrapper;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager.LayoutParams;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Toast;

public class MainActivity extends ActionBarActivity implements APICallback {

	private List<MenuLabel> drawerOptions;
	private PostListAdapter postListAdapter;
	private DrawerListAdapter drawerListAdapter;
    private SwipeRefreshLayout swipeRefreshLayout;
    private Folder selectedFolder;
    private Toolbar toolbar;
    
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        initBar();
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
						if (postListAdapter.getPostsCount()==0)
							new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
						else
							new APICall(MainActivity.this).execute(Content.get().getQuery(postListAdapter.getLastPostId()),"2");
						setLoading(true);
						postListAdapter.isLoading(true);
					}
					return;
				}
		        Intent intentApp = new Intent(MainActivity.this, PostViewActivity.class);
				intentApp.putExtra("postId", (String)p.getId());
				startActivityForResult(intentApp,37);
			}
		});  

		ListView drawer = (ListView) findViewById(R.id.drawer);
		drawer.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
				MenuLabel ml = drawerOptions.get(arg2);
				if (ml instanceof Folder){
					//Content.get().viewFolder(ml.getId());
					selectedFolder = (Folder) ml;
					List<CharSequence> a = new ArrayList<CharSequence>();
					a.add("All posts");
					for (Feed feed:selectedFolder.getFeeds()){
						a.add(feed.getLabel());
					}
					
					CharSequence[] titles = a.toArray(new CharSequence[a.size()]);
					AlertDialog.Builder builder = new AlertDialog.Builder(new ContextThemeWrapper(MainActivity.this, android.R.style.Theme_Holo_Dialog));
					builder.setTitle(R.string.feed_prompt);
					builder.setItems(titles, new DialogInterface.OnClickListener() {
					    @Override
					    public void onClick(DialogInterface dialog, int which) {
					    	if (which==0){
					    		Content.get().viewFolder(selectedFolder.getId());
					    		toolbar.setTitle(selectedFolder.getTitle());	
					    	} else {
						    	Feed f = selectedFolder.getFeeds().get(which-1);
								Content.get().viewFeed(f.getId());
								toolbar.setTitle(f.getTitle());
					    	}
							new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
							setLoading(true);
					    }
					});
					builder.show();
				} else if (ml instanceof Feed) {
					Content.get().viewFeed(ml.getId());
					toolbar.setTitle(ml.getTitle());
					new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
					setLoading(true);
				} else if (ml instanceof Tag) {
					Content.get().viewTag(ml.getId());
					toolbar.setTitle(ml.getTitle());
					new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
					setLoading(true);
				} else if (ml instanceof Label && ml.getLabel().equals(getString(R.string.all_posts))) {
					Content.get().viewAll();
					toolbar.setTitle(ml.getTitle());
					new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
					setLoading(true);
				} else return;

				((DrawerLayout)findViewById(R.id.drawer_layout)).closeDrawers();
			}
		});
		
		swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
		swipeRefreshLayout.setColorSchemeResources(R.color.green,R.color.purple,
        											R.color.yellow,R.color.orange);
		
		swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
            	new APICall(MainActivity.this).execute("arch?token="+Content.get().getToken()+"&lock="+Content.get().getLock());
		        //setLoadingScreen(true);
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
        new APICall(this).execute("arch?token="+Content.get().getToken()+"&lock="+Content.get().getLock());
    }

	@Override
	public void APIResponse(JSONObject json, int id, APICall parent) throws JSONException {
		switch(id){
			case 0: // ARCH
				Content.get().reloadStructure(json);
				updateDrawer();
				setLoadingScreen(false);
				new APICall(this).execute(Content.get().getQuery(),"1");
				setLoading(true);
				openOptionsMenu();closeOptionsMenu(); // Forces a call to onCreateOptionsMenu
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
				
			case 3: // Unlock
				String lockToken = json.getString("locktoken");
				Content.get().setLock(lockToken);
		        new APICall(this).execute("arch?token="+Content.get().getToken()+"&lock="+Content.get().getLock());
		        setLoadingScreen(true);
				break;
		}
			
	}

	
	public void updateDrawer(){
        ListView drawer = (ListView) findViewById(R.id.drawer);
		
        drawerOptions = new ArrayList<MenuLabel>();
        Collection<Folder> folders = Content.get().getFolders().values();
        
        Folder fnull = null;
        for (Folder f : folders){
        	if (f.getName().equals("null"))
        		fnull = f;
        	else
        		drawerOptions.add(f);
        }
		Collections.sort(drawerOptions, new LabelComparator());
		
        drawerOptions.add(0,new Label(getString(R.string.folders)));
        drawerOptions.add(0,new Label(getString(R.string.all_posts)));
		drawerOptions.add(new Label(getString(R.string.feeds)));
		
        if (fnull != null){
        	Collections.sort(fnull.getFeeds(), new LabelComparator());
	        for (Feed f : fnull.getFeeds()){
	        	drawerOptions.add(f);
	        }
        }
        
        drawerOptions.add(new Label(getString(R.string.tags)));
        
        List<Tag> tags = new ArrayList<Tag>(Content.get().getTags().values());
        Collections.sort(tags, new LabelComparator());
        
        for (Tag t : tags){
        	drawerOptions.add(t);
        }
        
        drawerListAdapter = new DrawerListAdapter(this, android.R.id.text1, drawerOptions);
        drawer.setAdapter(drawerListAdapter);
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

    	toolbar.setMenu((MenuBuilder)menu, new ActionMenuPresenter(this));
    	
        return true;
    }
    
    private boolean mZ = true;
    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {
        menu.clear(); //Clear view of previous menu
        MenuInflater inflater = getMenuInflater();
        if(mZ)
            inflater.inflate(R.menu.main, menu);
        else
            inflater.inflate(R.menu.post_menu, menu);
        
        return super.onPrepareOptionsMenu(menu);
    }
    
    @Override
    public boolean onKeyDown(int keycode, KeyEvent e) {
        switch(keycode) {
            case KeyEvent.KEYCODE_MENU:
                return false;
        }
        return super.onKeyDown(keycode, e);
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
	        case R.id.action_settings:	        	
	        	break;
	        case R.id.action_unlock:
	        	LockDialog cdd=new LockDialog(this);
	        	cdd.show();
	        	break;
	        case R.id.action_exit:
	        	moveTaskToBack(true);
	        	break;
	        case R.id.action_unread:
	        	boolean u = Content.get().toggleUnread();
	        	if (u)	item.setIcon(R.drawable.ic_unread);
	        	else	item.setIcon(R.drawable.ic_read);
            	new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
				setLoading(true);
	        	break;
	        case R.id.action_fav:
	        	boolean f = Content.get().toggleFavorites();
	        	if (f)	item.setIcon(R.drawable.ic_fav);
	        	else	item.setIcon(R.drawable.ic_unfav);
            	new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
				setLoading(true);
	        	break;
	        case R.id.action_order:
	        	boolean o = Content.get().toggleOrder();
	        	if (o)	item.setIcon(R.drawable.ic_newer);
	        	else	item.setIcon(R.drawable.ic_older);
            	new APICall(MainActivity.this).execute(Content.get().getQuery(),"1");
				setLoading(true);
	        	break;
	        default:
	        	return true;
        }

        return super.onOptionsItemSelected(item);
    }
    
    @Override
    public void onBackPressed() {
        moveTaskToBack(true);
    }
    
    private void initBar(){
    	toolbar = (Toolbar)findViewById(R.id.toolbar_actionbar);
    	//setSupportActionBar(t);
    	toolbar.setOnMenuItemClickListener(new OnMenuItemClickListener(){

			@Override
			public boolean onMenuItemClick(MenuItem arg0) {
				// TODO Auto-generated method stub
				return false;
			}
    		
    	});
    	//t.setMenu((MenuBuilder)m, new ActionMenuPresenter(this));
    	
    	toolbar.setTitle(this.getTitle());



    	toolbar.setNavigationIcon(R.drawable.ic_drawer);
    	

    	
    	DrawerLayout dl=(DrawerLayout)findViewById(R.id.drawer_layout);
    	
    	ActionBarDrawerToggle mDrawerToggle = new ActionBarDrawerToggle(this, dl,toolbar,R.string.login, R.string.logout);
        dl.setDrawerListener(mDrawerToggle);

        //getSupportActionBar().setDisplayHomeAsUpEnabled(true);
    }
    
    @Override
    public void onResume(){
    	super.onResume();
    	if (postListAdapter!=null)
    		postListAdapter.notifyDataSetChanged();
    	if (drawerListAdapter!=null)
    		drawerListAdapter.notifyDataSetChanged();
    }

    
    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 37 && resultCode == 21) {
			int idx = postListAdapter.getPostsCount();//Content.get().getOrderedPosts().size();
			List<Post> posts = Content.get().getOrderedPosts();
			for (int j=idx;j<posts.size();j++){
				postListAdapter.add(posts.get(j));
			}
			
	        postListAdapter.notifyDataSetChanged();
        }
   }
    
    public void setLock(String lock){
    	new APICall(this).execute("unlock?token="+Content.get().getToken()+"&pass="+lock,"3");
    }
    
    public void removeLock(){
    	Content.get().setLock("");
        new APICall(this).execute("arch?token="+Content.get().getToken()+"&lock="+Content.get().getLock());
        setLoadingScreen(true);
    }
}
