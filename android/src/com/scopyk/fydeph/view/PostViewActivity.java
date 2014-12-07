package com.scopyk.fydeph.view;

import java.text.DateFormat;
import java.text.SimpleDateFormat;

import org.json.JSONException;
import org.json.JSONObject;

import com.scopyk.fydeph.APICall;
import com.scopyk.fydeph.APICallback;
import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Point;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.widget.Toolbar;
import android.util.DisplayMetrics;
import android.view.Display;
import android.view.DragEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnDragListener;
import android.view.View.OnTouchListener;
import android.view.WindowManager;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.TextView;
import android.widget.Toast;

public class PostViewActivity extends ActionBarActivity implements APICallback {

	private Post post;
	private Menu menu;
	
	private WebView wv;
	
	private boolean addedPosts;
	private boolean loadingNewPosts;
	
	//Mouse Events DoubleTap:
    int clickCount = 0;
    long startTime = 0;
    static final int MAX_DURATION = 1000;
    static final int MIN_DURATION = 500;
    private float lastScrollX,lastX;
    private int moveDragStepL = 0;
    private int moveDragStepR = 0;
    private int screenWidth = 0;
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_postviewer);
        addedPosts=false;
        loadingNewPosts=false;
        
		findViewById(R.id.loading_icon).setVisibility(View.INVISIBLE);
        Display display = getWindowManager().getDefaultDisplay();
        Point size = new Point();
        display.getSize(size);
        screenWidth = size.x;
        
        String postid = this.getIntent().getStringExtra("postId");
        this.post = Content.get().getPost(postid);
    	Toolbar t = (Toolbar)findViewById(R.id.toolbar_actionbar);
    	t.setOnClickListener(new OnClickListener(){
			@Override
			public void onClick(View v) {
				Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(post.getLink()));
				startActivity(browserIntent);
			}
    	});
    	setSupportActionBar(t);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        wv = (WebView)findViewById(R.id.html_content);
        
        WebSettings ws = wv.getSettings();
        wv.setHorizontalScrollBarEnabled(false);
        wv.setVerticalScrollBarEnabled(false);

        ws.setUseWideViewPort(true);
        ws.setBuiltInZoomControls(true);
        ws.setSupportZoom(true); 
        ws.setLoadWithOverviewMode(true);
        ws.setDisplayZoomControls(false);

        wv.setWebChromeClient(new WebChromeClient(){
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                super.onProgressChanged(view, newProgress);
                //findViewById(R.id.loading_icon).setVisibility(View.INVISIBLE);
            }
        });
        loadPost(post);
        wv.setOnTouchListener(new OnTouchListener(){
			@Override
			public boolean onTouch(View v, MotionEvent event) {
		        switch(event.getAction() & MotionEvent.ACTION_MASK){
			        case MotionEvent.ACTION_DOWN:
			        	if ((System.currentTimeMillis() - startTime)>MAX_DURATION){
			        		startTime = System.currentTimeMillis();
			        		clickCount=1;
			        	} else {
			        		clickCount++;
			        	}

			            lastScrollX=wv.getScrollX();
			            lastX=event.getX();
			            moveDragStepL=1;
			            moveDragStepR=1;
			            break;
			        case MotionEvent.ACTION_MOVE:
			        	if (moveDragStepL==1 && wv.getScrollX()==lastScrollX && event.getX()<lastX)
			        		moveDragStepL=2;
			        	else if (moveDragStepR==1 && wv.getScrollX()==lastScrollX && event.getX()>lastX)
			        		moveDragStepR=2;
			        	break;
			        case MotionEvent.ACTION_UP:
			            if (moveDragStepL==2 && wv.getScrollX()==lastScrollX && event.getX()<(lastX-screenWidth/3)){
				        	nextPost();
			            }
			            else if (moveDragStepR==2 && wv.getScrollX()==lastScrollX && event.getX()>(lastX+screenWidth/3)){
			            	prevPost();
			            }
			            /*else if (clickCount==2){
			            	long duration = System.currentTimeMillis() - startTime;
			                if(MIN_DURATION <= duration && duration<= MAX_DURATION){
			                    toggleUnread();
			                }
			                clickCount = 0;
			                duration = 0;   
			                return true;
			            }*/
			            moveDragStepL=0;
			            moveDragStepR=0;
		                break;
		        }
		        return false;  
			}
        });
    }

    private void loadPost(Post p){
        WebView wv = (WebView)findViewById(R.id.html_content);
        final String mimeType = "text/html";
        final String encoding = "UTF-8";
        
        //final String style = "<style>body{width:100wv;position:absolute;font-size:1em}p,b,h1,h2,h3,h4,h5,h6,div,img{background-color:#fff;}table{width:100wv;}table img{width:initial}</style>";//,unset
        final String style = "<style>body{font-size:1em;}p,b,h1,h2,h3,h4,h5,h6,div,img{height:auto;width:100%;background-color:#fff;}table{width:100wv;}table img{width:initial}</style>";//,unset
        wv.loadDataWithBaseURL("", style+post.getDescription(), mimeType, encoding, "");
        
    	Toolbar t = (Toolbar)findViewById(R.id.toolbar_actionbar);   	
    	setTitle(post.getTitle());
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
    	t.setSubtitle(df.format(post.getDate()));
    	
    	if (this.menu != null)
    		updateIcons();
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.post_menu, menu);
        this.menu = menu;
        updateIcons();
    	markAsRead();
        return true;
    }
    
    public void updateIcons(){
        MenuItem unread = menu.findItem(R.id.action_unread);
        MenuItem fav = menu.findItem(R.id.action_fav);
    	if (post.getUnread())	unread.setIcon(R.drawable.ic_unread);
    	else					unread.setIcon(R.drawable.ic_read);
    	if (post.getFavorite())	fav.setIcon(R.drawable.ic_fav);
    	else					fav.setIcon(R.drawable.ic_unfav);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        switch(id){
	        case R.id.action_prev:
	        	prevPost();
	        	break;
	        case R.id.action_next:
	        	nextPost();
	        	break;
	        case R.id.action_unread:
	        	toggleUnread();
	        	break;
	        case R.id.action_fav:
	        	toggleFavorite();
	        	break;
	        default:
	        	finish();
	        	return true;
        }
        return super.onOptionsItemSelected(item);
    }

    public void nextPost(){
    	Post pnext = Content.get().getNextPost(post);
    	if (pnext==post){
    		if (!loadingNewPosts){
	    		new APICall(PostViewActivity.this).execute(Content.get().getQuery(post.getId()),"3");
	    		loadingNewPosts=true;
    		}
    	} else {
    		post = pnext;
        	loadPost(post);
        	markAsRead();
    	}
    }
    
    public void prevPost(){
    	Post pprev = Content.get().getPrevPost(post);
    	if (pprev==post){
    	} else {
    		post = pprev;
        	loadPost(post);
        	//markAsRead();
    	}
    }
    
	@Override
	public void APIResponse(JSONObject json, int id, APICall parent) throws JSONException {
    	findViewById(R.id.loading_icon).setVisibility(View.INVISIBLE);
		Post p = (Post) parent.getContent();
		switch (id){
			case 1:	//unread
				int i = json.getInt("unread");
				if (i==1){
					p.setUnread(true);
					post.getFeed().addCount(1);
					post.getFeed().getFolder().addCount(1);
				} else {
					p.setUnread(false);
					post.getFeed().addCount(-1);
					post.getFeed().getFolder().addCount(-1);
				}
				if (p==post) updateIcons();
				break;
			case 2:	//fav
				int j = json.getInt("favorite");
				p.setFavorite(j==1);
				if (p==post) updateIcons();
				break;
			case 3:	//loadmore
				loadingNewPosts = false;
				Content.get().addPosts(json);
	        	addedPosts=true;
				post = Content.get().getNextPost(post);
	        	loadPost(post);
	        	markAsRead();
				break;
		}
	}
	
	private void toggleFavorite(){
		findViewById(R.id.loading_icon).setVisibility(View.VISIBLE);
    	String f="token="+Content.get().getToken()+"&lock="+Content.get().getLock()+"&postid="+post.getId()+"&fav=";
    	if (post.getFavorite()) 	f+="0";
    	else					f+="1";
    	new APICall(PostViewActivity.this,post).execute("update_post?"+f,"2");
	}
	
	private void toggleUnread(){
		findViewById(R.id.loading_icon).setVisibility(View.VISIBLE);
    	String l="token="+Content.get().getToken()+"&lock="+Content.get().getLock()+"&postid="+post.getId()+"&unread=";
    	if (post.getUnread()) 	l+="0";
    	else					l+="1";
    	new APICall(PostViewActivity.this,post).execute("update_post?"+l,"1");
	}
	
	private void markAsRead(){
		if (post.getUnread()){
			findViewById(R.id.loading_icon).setVisibility(View.VISIBLE);
			String l="token="+Content.get().getToken()+"&lock="+Content.get().getLock()+"&postid="+post.getId()+"&unread=0";
			new APICall(PostViewActivity.this,post).execute("update_post?"+l,"1");
		}
	}
	
	@Override
	public void finish(){
		if (addedPosts){
			Intent resultIntent = new Intent();
			setResult(21, resultIntent);
		}
		super.finish();
	}
}
