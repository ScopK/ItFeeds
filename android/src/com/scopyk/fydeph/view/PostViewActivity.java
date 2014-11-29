package com.scopyk.fydeph.view;

import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.*;

import android.app.Activity;
import android.os.Bundle;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;

public class PostViewActivity extends Activity {

	private Post post;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_postviewer);
        
        String postid = this.getIntent().getStringExtra("postId");
        this.post = Content.get().getPost(postid);
        
        WebView wv = (WebView)findViewById(R.id.html_content);
        final String mimeType = "text/html";
        final String encoding = "UTF-8";
        final String style = "<style>body{font-size:3em;}p,b,h1,h2,h3,h4,h5,h6,div,img{width:100%;background-color:#fff;}table{width:100wv;}table img{width:initial}</style>";//,unset
        wv.loadDataWithBaseURL("", style+post.getDescription(), mimeType, encoding, "");
        
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
    }
}
