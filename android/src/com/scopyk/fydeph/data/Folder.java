package com.scopyk.fydeph.data;

import java.util.ArrayList;

public class Folder implements MenuLabel {
	private String id;
	private String name;
	private ArrayList<Feed> feeds;
	private boolean hidden;
	private int unread;
	private int count;
	
	public Folder(String id,String name,boolean hidden,int unread,int count){
		this(id,name);
		this.hidden = hidden;
		this.unread = unread;
		this.count = count;
	}
	
	public Folder(String id,String name){
		this.id = id;
		this.name = name;
		this.feeds = new ArrayList<Feed>();
	}
	
	public ArrayList<Feed> getFeeds() {
		return feeds;
	}
	public void addFeeds(Feed feed) {
		this.feeds.add(feed);
	}
	public boolean isHidden() {
		return hidden;
	}
	public void setHidden(boolean hidden) {
		this.hidden = hidden;
	}
	public int getUnread() {
		return unread;
	}
	public void setUnread(int unread) {
		this.unread = unread;
	}
	public int getCount() {
		return count;
	}
	public void setCount(int count) {
		this.count = count;
	}
	public String getId(){
		return this.id;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getName(){
		return this.name;
	}

	@Override
	public String getLabel() {
		if (this.unread > 0)
			return this.name+" ("+this.unread+")";
		else
			return this.name;
	}	
}
