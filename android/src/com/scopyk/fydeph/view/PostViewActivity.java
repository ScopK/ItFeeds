package com.scopyk.fydeph.view;

import java.text.DateFormat;
import java.text.SimpleDateFormat;

import com.scopyk.fydeph.APICall;
import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.TextView;

public class PostViewActivity extends ActionBarActivity {

	private Post post;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_postviewer);
        
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
        getSupportActionBar().setDisplayHomeAsUpEnabled(false);

        WebView wv = (WebView)findViewById(R.id.html_content);
        
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
            	//Toast.makeText(ViewerActivity.this.getApplicationContext(), newProgress+"", 1).show();
            }
        });
        loadPost(post);
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
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.post_menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        switch(id){
	        case R.id.action_prev:
	        	post = Content.get().getPrevPost(post);
	        	loadPost(post);
	        	break;
	        case R.id.action_next:
	        	post = Content.get().getNextPost(post);
	        	loadPost(post);
	        	break;
	        case R.id.action_unread:
	        	break;
	        case R.id.action_fav:
	        	break;
	        default:
	        		return true;
        }

        return super.onOptionsItemSelected(item);
    }
}
