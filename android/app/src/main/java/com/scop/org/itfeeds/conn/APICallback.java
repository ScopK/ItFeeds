package com.scop.org.itfeeds.conn;

import org.json.JSONException;

public interface APICallback {
	public void APIResponse(org.json.JSONObject json, int id, APICall parent) throws JSONException;
	public android.content.Context getApplicationContext();
}
