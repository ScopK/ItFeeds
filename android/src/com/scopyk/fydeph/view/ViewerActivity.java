package com.scopyk.fydeph.view;

import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.webkit.WebSettings;
import android.webkit.WebView;


public class ViewerActivity extends Activity {

	private Post post;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.viewer);
        
        String postid = this.getIntent().getStringExtra("postId");
        this.post = Content.get().getPost(postid);
        
        WebView wv = (WebView)findViewById(R.id.html_content);
        final String mimeType = "text/html";
        final String encoding = "UTF-8";
        wv.loadDataWithBaseURL("", post.getDescription(), mimeType, encoding, "");
        
        WebSettings ws = wv.getSettings();
        wv.setHorizontalScrollBarEnabled(false);
        wv.setVerticalScrollBarEnabled(false);

        ws.setUseWideViewPort(true);
        ws.setBuiltInZoomControls(true);
        ws.setSupportZoom(true); 
        ws.setLoadWithOverviewMode(true);
        ws.setDisplayZoomControls(false);
    }

}
