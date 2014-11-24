package com.scopyk.fydeph.data;

import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class Content {
	private static Content c=null;
	public static Content get(){
		if (c==null) c=new Content();
		return c;
	}
	
	private HashMap<String,Folder> folders;
	private HashMap<String,Tag> tags;
	private HashMap<String,Post> posts;
	private String token;
	
	private Content(){
		this.folders = new HashMap<String,Folder>();
		this.tags = new HashMap<String,Tag>();
		this.posts = new HashMap<String,Post>();
	}
	
	public void setToken(String token){
		this.token = token;
	}
	public String getToken(){
		return this.token;
	}
	
	public void reloadStructure(JSONObject json) throws JSONException{
		this.folders = new HashMap<String,Folder>();
		this.tags = new HashMap<String,Tag>();
		this.posts = new HashMap<String,Post>();
		
		JSONArray folders = json.getJSONArray("folders");
		for (int i=0;i<folders.length();i++){
			JSONObject f = (JSONObject)folders.get(i);
			Folder folder = new Folder(f.getString("id"),f.getString("name"));
			folder.setHidden(f.getInt("hidden")==1);
			folder.setUnread(f.getInt("unread"));
			folder.setCount(f.getInt("count"));
			JSONArray feeds = f.getJSONArray("feeds");
			for (int j=0;j<feeds.length();j++){
				JSONObject ff = (JSONObject)feeds.get(j);
				
				Feed feed = new Feed(ff.getString("id"),ff.getString("name"));
				feed.setLink(ff.getString("link"));
				feed.setRssLink(ff.getString("rss_link"));
				feed.setCount(ff.getInt("count"));
				feed.setUnread(ff.getInt("unread"));
				//ff.getInt("last_date_post")
				//ff.getInt("upd_time")
				
				feed.setFolder(folder);
				folder.addFeeds(feed);
			}
			this.folders.put(f.getString("id"),folder);
		}
		
		JSONArray tags = json.getJSONArray("tags");
		for (int i=0;i<tags.length();i++){
			JSONObject t = (JSONObject)tags.get(i);
			Tag tag = new Tag(t.getString("id"),t.getString("name"));
			tag.setCount(t.getInt("count"));
			tag.setPublic(t.getInt("public")==1);
			tag.setHidden(t.getInt("hidden")==1);

			this.tags.put(t.getString("id"),tag);
		}
	}
}
