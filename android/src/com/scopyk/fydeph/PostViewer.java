package com.scopyk.fydeph;


import org.json.JSONException;
import org.json.JSONObject;

import com.scopyk.fydeph.data.Content;

import android.app.Activity;
import android.os.Bundle;
import android.widget.ListView;

public class PostViewer extends Activity implements APICallback {

	private String token;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.post_viewer);
        
        this.token = Content.get().getToken();
        new APICall(this).execute("arch?token="+token);
    }
	
	@Override
	public void APIResponse(JSONObject json) throws JSONException {
		System.out.println(json.getJSONArray("folders").length());
		setTitle("LOADING");
		Content.get().reloadStructure(json);
		setTitle("LOADED");
		
		//ListView lv = (ListView) findViewById(R.id.left_drawer);
	}

}
